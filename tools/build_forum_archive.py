#!/usr/bin/env python3
"""
Erzeugt aus der ThWboard-Datenbank (apboard) ein statisches, schreibgeschütztes
HTML-Archiv unter neu/forum/.

- Es werden nur Bretter übernommen, die Gäste im alten Forum lesen durften.
- Private Nachrichten, E-Mail-Adressen, IPs und Passwort-Hashes werden nicht gelesen.
- Seitenkopf und -fuß werden von der laufenden neuen Seite übernommen, damit
  Navigation und Design identisch sind.

Aufruf:  python3 tools/build_forum_archive.py
"""

import html
import json
import math
import os
import re
import shutil
import subprocess
import sys
import urllib.request
from datetime import datetime
from zoneinfo import ZoneInfo

# --- Konfiguration -----------------------------------------------------------

DB_CONTAINER = os.environ.get('NL_DB_CONTAINER', 'nela-mysql-restore')
DB_USER = os.environ.get('NL_DB_USER', 'root')
DB_PASS = os.environ.get('NL_DB_PASS')  # Pflicht, z. B. per .env (siehe .env.example)
if not DB_PASS:
    sys.exit('Bitte NL_DB_PASS setzen (MySQL-Passwort fuer ' + DB_CONTAINER + ').')
DB_NAME = os.environ.get('NL_DB_NAME', 'apboard')
SITE_URL = os.environ.get('NL_SITE_URL', 'http://192.168.178.229/neu/index.php?s=news')

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
OUT = os.path.join(ROOT, 'neu', 'forum')

GUEST_GROUP = 2
THREADS_PER_PAGE = 40
POSTS_PER_PAGE = 25
TZ = ZoneInfo('Europe/Berlin')
MONTHS = ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August',
          'September', 'Oktober', 'November', 'Dezember']


# --- Datenbank ---------------------------------------------------------------

def _unescape_batch(value):
    """mysql --batch maskiert \\n, \\t, \\\\ und \\0."""
    if value == 'NULL':
        return None
    return re.sub(r'\\(.)', lambda m: {'n': '\n', 't': '\t', '0': '\0', '\\': '\\'}.get(m.group(1), m.group(1)), value)


def query(sql):
    cmd = ['docker', 'exec', DB_CONTAINER, 'mysql', '-u' + DB_USER, '-p' + DB_PASS,
           '--default-character-set=utf8', '--batch', DB_NAME, '-e', sql]
    out = subprocess.run(cmd, capture_output=True, check=True).stdout.decode('utf-8')
    lines = out.split('\n')
    if not lines or not lines[0]:
        return []
    header = lines[0].split('\t')
    rows = []
    for line in lines[1:]:
        if line == '':
            continue
        rows.append(dict(zip(header, (_unescape_batch(v) for v in line.split('\t')))))
    return rows


# --- Hilfsfunktionen ---------------------------------------------------------

def esc(text):
    return html.escape(text or '', quote=True)


def fmt_date(ts):
    ts = int(ts or 0)
    if ts <= 0:
        return ''
    d = datetime.fromtimestamp(ts, TZ)
    return f'{d.day}. {MONTHS[d.month - 1]} {d.year}, {d:%H:%M} Uhr'


def fmt_day(ts):
    ts = int(ts or 0)
    if ts <= 0:
        return ''
    d = datetime.fromtimestamp(ts, TZ)
    return f'{d:%d.%m.%Y}'


def page_name(prefix, ident, page):
    return f'{prefix}{ident}.html' if page == 1 else f'{prefix}{ident}-{page}.html'


def plural(n, one, many):
    return f'{n:,} {one if n == 1 else many}'.replace(',', '.')


# --- Umlaut-Reparatur --------------------------------------------------------
#
# Das Backup von 2007 hat bei den meisten Beiträgen ä/ö/ü/ß durch "?" ersetzt.
# Die Reparatur wählt für jedes betroffene Wort die wahrscheinlichste Schreibung:
# erst per Worthäufigkeitsliste, sonst per Buchstaben-Trigramm-Statistik.

import itertools

LETTERS = 'A-Za-zÄÖÜäöüß'
MID_CANDIDATES = ['ü', 'ä', 'ö', 'ß']
START_LOWER = ['ü', 'ä', 'ö']
ARTICLES = {'der', 'die', 'das', 'den', 'dem', 'des', 'ein', 'eine', 'einen', 'einem', 'einer', 'eines', 'kein', 'keine', 'keinen', 'mein', 'meine', 'dein', 'deine', 'sein', 'seine', 'ihr', 'ihre', 'unser', 'unsere', 'jeder', 'jede', 'jedes', 'dieser', 'diese', 'dieses', 'zum', 'zur', 'im', 'am', 'vom', 'beim'}
GREETING_WORDS = {'schöne', 'liebe', 'viele', 'freundliche', 'freundlichen', 'herzliche', 'herzlichen', 'beste', 'besten', 'nette', 'netten', 'mit'}


class _FakeMatch:
    def __init__(self, s, start, end):
        self._s, self._start, self._end = s, start, end

    def group(self, _=0):
        return self._s

    def start(self):
        return self._start

    def end(self):
        return self._end


class UmlautRepair:
    def __init__(self, wordlist_path, extra_texts):
        self.freq = {}
        with open(wordlist_path, encoding='utf-8') as f:
            for line in f:
                parts = line.split()
                if len(parts) == 2 and parts[1].isdigit():
                    self.freq[parts[0].lower()] = self.freq.get(parts[0].lower(), 0) + int(parts[1])
        # Zusätzliche Wörter aus korrekt kodierten Texten (Seite, intakte Beiträge)
        for text in extra_texts:
            for w in re.findall(f'[{LETTERS}]+', text):
                if re.search('[äöüß]', w.lower()):
                    self.freq[w.lower()] = self.freq.get(w.lower(), 0) + 500
        # Trigramm-Modell aus der Wortliste
        self.tri = {}
        self.bi = {}
        for w, n in self.freq.items():
            weight = math.log(1 + n)
            s = f'^{w}$'
            for i in range(len(s) - 2):
                self.tri[s[i:i + 3]] = self.tri.get(s[i:i + 3], 0) + weight
                self.bi[s[i:i + 2]] = self.bi.get(s[i:i + 2], 0) + weight
        # Alle Wortanfänge (mind. 4 Zeichen) für die Bewertung unbekannter Wörter
        self.prefixes = {}
        for w, n in self.freq.items():
            for k in range(4, len(w) + 1):
                key = w[:k]
                if self.prefixes.get(key, 0) < n:
                    self.prefixes[key] = n
        self.stats = {'woerter': 0, 'wb': 0, 'statistik': 0}

    def tri_score(self, word):
        s = f'^{word}$'
        score = 0.0
        for i in range(len(s) - 2):
            score += math.log((self.tri.get(s[i:i + 3], 0) + 0.1) / (self.bi.get(s[i:i + 2], 0) + 1))
        return score

    def best(self, token, options_per_pos):
        positions = [i for i, ch in enumerate(token) if ch == '?']
        combos = itertools.product(*options_per_pos)
        candidates = []
        for combo in combos:
            chars = list(token)
            for pos, rep in zip(positions, combo):
                chars[pos] = rep
            candidates.append(''.join(chars))
        known = [(self.freq.get(c.lower(), 0), c) for c in candidates if c.lower() in self.freq]
        if known:
            self.stats['wb'] += 1
            return max(known)[1]
        # Zusammengesetzte Wörter: bekannten Wortteil um die ersetzte Stelle suchen
        def position_score(low, pos):
            best_score = 0.0
            for i in range(0, pos + 1):
                for j in range(max(pos + 1, i + 4), len(low) + 1):
                    n = self.freq.get(low[i:j], 0)
                    if n:
                        best_score = max(best_score, (j - i) * math.log(1 + n))
            # Bonus, wenn ein Wortteil ab einer Fuge wie ein bekanntes Wort beginnt
            for i in range(0, pos + 1):
                for j in range(len(low), pos, -1):
                    n = self.prefixes.get(low[i:j], 0)
                    if n and j - i >= 5:
                        best_score = max(best_score, (j - i) * math.log(1 + n) * 1.2)
                        break
            return best_score

        def part_score(cand):
            low = cand.lower()
            # Jede ersetzte Stelle muss für sich plausibel sein
            return sum(position_score(low, pos) for pos in positions) / len(positions) \
                if all(position_score(low, pos) > 0 for pos in positions) else 0.0
        scored = [(part_score(c), c) for c in candidates]
        top = max(scored)
        if top[0] > 0:
            self.stats['komposita'] = self.stats.get('komposita', 0) + 1
            return top[1]
        self.stats['statistik'] += 1
        return max(candidates, key=lambda c: self.tri_score(c.lower()))

    def fix(self, text):
        if not text or '?' not in text:
            return text

        def repl(m):
            token = m.group(0)
            if not re.search(f'[{LETTERS}]', token):
                return token
            # "??" am Ende ist Satzzeichen, mitten im Wort (Gr??e) dagegen zwei Umlaute
            if token.startswith('??'):
                return token
            run = re.search(r'\?{2,}$', token)
            if run:
                inner = token[:run.start()]
                if not re.search(f'[{LETTERS}]', inner):
                    return token
                fixed = repl(_FakeMatch(inner, m.start(), m.start() + len(inner)))
                return fixed + run.group(0)
            start, end = m.start(), m.end()
            after = text[end:end + 2]
            trailing = token.endswith('?')
            core = token[:-1] if trailing else token
            if '?' not in core and not trailing:
                return token

            # Wortende: nur ß, wenn kein Fragesatz folgt und das Wort damit Sinn ergibt
            suffix = ''
            if trailing:
                looks_like_question = after == '' or after[:1] in '\n' or (after[:1] == ' ' and (after[1:2].isupper() or after[1:2] in ('', '\n'))) or after[:1] in '!?)"'
                with_ss = core.lower() + 'ß'
                if '?' in core:
                    with_ss = None
                if with_ss and not looks_like_question and with_ss in self.freq and self.freq[with_ss] > self.freq.get(core.lower(), 0) / 50:
                    core = core + 'ß'
                elif with_ss and with_ss in self.freq and core.lower() not in self.freq:
                    core = core + 'ß'
                else:
                    suffix = '?'
            if '?' not in core:
                self.stats['woerter'] += 1
                return core + suffix

            # Verlorenes Anführungszeichen („) am Wortanfang: ?Wort bzw. ?ich
            if core.startswith('?') and len(core) > 1 and '?' not in core[1:]:
                rest = core[1:]
                umlaut_known = any((u + rest).lower() in self.freq for u in START_LOWER)
                if rest[0].isupper() or (rest.lower() in self.freq and not umlaut_known):
                    self.stats['woerter'] += 1
                    return '„' + rest + suffix

            # Grußformeln: "Gr??e" ist im Forum fast immer "Grüße", nicht "Größe"
            if core.lower() in ('gr??e', 'gr??en'):
                before = re.findall(f'[{LETTERS}]+', text[max(0, start - 25):start])
                prev = before[-1].lower() if before else ''
                nxt = re.match(r'\s*([^\s]*)', text[end:]).group(1).lower()
                if prev in GREETING_WORDS or nxt in ('an', 'aus', 'von', 'und', 'zurück', '') or text[end:end + 1] in (',', '!', '\n', ''):
                    self.stats['woerter'] += 1
                    return core[0:2] + 'üß' + core[4:] + suffix

            # Optionen je Fragezeichen-Position
            options = []
            for i, ch in enumerate(core):
                if ch != '?':
                    continue
                if i == 0:
                    sentence_start = start == 0 or re.search(r'(^|[.!?:\n]\s*)$', text[max(0, start - 3):start]) is not None
                    prev_words = re.findall(f'[{LETTERS}]+', text[max(0, start - 15):start])
                    if prev_words and prev_words[-1].lower() in ARTICLES and re.search(r'\s$', text[:start]):
                        sentence_start = True  # nach Artikel folgt meist ein Substantiv
                    opts = START_LOWER
                    if sentence_start or (len(core) > 1 and core[1].isupper()):
                        opts = [o.upper() for o in opts]
                    options.append(opts)
                else:
                    opts = MID_CANDIDATES
                    if core[i - 1].isupper() and (i + 1 < len(core) and core[i + 1].isupper()):
                        opts = ['Ü', 'Ä', 'Ö']
                    options.append(opts)
            if len(options) > 4:
                return token
            self.stats['woerter'] += 1
            return self.best(core, options) + suffix

        # URLs (enthalten echte "?") von der Reparatur ausnehmen
        parts = re.split(r'((?:https?|ftp)://\S+|www\.\S+|\[url=[^\]]*\]|\S+\.php\?\S*)', text)
        for i in range(0, len(parts), 2):
            text = parts[i]
            parts[i] = re.sub(f'[{LETTERS}?]+', repl, text)
        return ''.join(parts)


# --- ThWBCode → HTML ---------------------------------------------------------

class Converter:
    def __init__(self, public_threads):
        self.public_threads = public_threads

    def link(self, url, label):
        """Gibt einen sicheren Link zurück; Links ins alte Forum zeigen ins Archiv."""
        url = url.strip()
        m = re.search(r'showtopic\.php\?.*?threadid=(\d+)', url)
        if m:
            tid = int(m.group(1))
            if tid in self.public_threads:
                return f'<a href="t{tid}.html">{label}</a>'
            return label
        if not re.match(r'^(https?|ftp)://', url, re.I):
            return label
        return f'<a href="{esc(url)}" rel="nofollow noopener noreferrer" target="_blank">{label}</a>'

    def convert(self, text):
        text = (text or '').replace('\r', '').replace('\\"', '"').replace("\\'", "'")
        slots = []

        def keep(fragment):
            slots.append(fragment)
            return f'\x00{len(slots) - 1}\x00'

        # Quelltext-Blöcke und [noparse] zuerst sichern
        text = re.sub(r'\[(code|php)\]\s*(.*?)\[/\1\]',
                      lambda m: keep('<pre>' + esc(m.group(2).strip('\n')) + '</pre>'), text, flags=re.S | re.I)
        text = re.sub(r'\[noparse\](.*?)\[/noparse\]', lambda m: keep(esc(m.group(1))), text, flags=re.S | re.I)

        text = html.escape(text, quote=False)
        # Im Text getippte E-Mail-Adressen nicht veröffentlichen
        text = re.sub(r'(?<![/\w.+-])[\w.+-]+@[\w-]+(?:\.[\w-]+)+', '[E-Mail-Adresse entfernt]', text)

        # Links, Mails, Bilder (Bilder lagen auf fremden, längst toten Servern → Link)
        text = re.sub(r'\[url=(?:&quot;|")?((?:https?|ftp)://[^\]"]+?|www\.[^\]"]+?)(?:&quot;|")?\](.*?)\[/url\]',
                      lambda m: keep(self.link(('http://' + m.group(1)) if m.group(1).lower().startswith('www.') else m.group(1), m.group(2))),
                      text, flags=re.S | re.I)
        text = re.sub(r'\[url\]\s*([a-z]+://[^\s\[]+|www\.[^\s\[]+)\s*\[/url\]',
                      lambda m: keep(self.link(('http://' + m.group(1)) if m.group(1).lower().startswith('www.') else m.group(1), m.group(1))),
                      text, flags=re.I)
        text = re.sub(r'\[img\]\s*((?:https?)://[^\s\[]+)\s*\[/img\]',
                      lambda m: keep(self.link(m.group(1), '[Bild]')), text, flags=re.I)
        text = re.sub(r'\[mail(?:=(?:&quot;|")?[^\]]*?(?:&quot;|")?)?\](.*?)\[/mail\]',
                      lambda m: '[E-Mail-Adresse entfernt]', text, flags=re.S | re.I)
        text = re.sub(r'(^|[\s(])((?:https?|ftp)://[^\s<\[]+?|www\.[^\s<\[]+?)([.,!?:;)]*)(?=\s|$|<|\[)',
                      lambda m: m.group(1) + keep(self.link(('http://' + m.group(2)) if m.group(2).lower().startswith('www.') else m.group(2), m.group(2))) + m.group(3),
                      text, flags=re.I | re.M)

        # Einfache Formatierungen (wiederholt, damit Verschachtelung klappt)
        rules = [
            (r'\[b\](.*?)\[/b\]', r'<strong>\1</strong>'),
            (r'\[i\](.*?)\[/i\]', r'<em>\1</em>'),
            (r'\[u\](.*?)\[/u\]', r'<u>\1</u>'),
            (r'\[-\](.*?)\[/-\]', r'<s>\1</s>'),
            (r'\[color=(?:&quot;|")?([a-zA-Z0-9# ]+?)(?:&quot;|")?\](.*?)\[/color\]', r'<span style="color:\1">\2</span>'),
            (r'\[c=(?:&quot;|")?(#?[a-zA-Z0-9]+)(?:&quot;|")?\](.*?)\[/c\]', r'<span style="color:\1">\2</span>'),
            (r'\[glow=(#?[a-zA-Z0-9]+)(?:,\d+)?\](.*?)\[/glow\]', r'<span style="text-shadow:0 0 6px \1">\2</span>'),
            (r'\[list\](.*?)\[/list\]', lambda m: '<ul>' + ''.join(
                f'<li>{item.strip()}</li>' for item in re.split(r'\[\*\]', m.group(1))[1:]) + '</ul>'),
        ]
        for _ in range(4):
            before = text
            for pattern, repl in rules:
                text = re.sub(pattern, repl, text, flags=re.S | re.I)
            if text == before:
                break

        # Zitate mit sauberer Verschachtelung
        out, depth = [], 0
        for part in re.split(r'(\[quote\]|\[/quote\])', text, flags=re.I):
            low = part.lower()
            if low == '[quote]':
                out.append('<blockquote><span class="fq-label">Zitat</span>')
                depth += 1
            elif low == '[/quote]':
                if depth:
                    out.append('</blockquote>')
                    depth -= 1
            else:
                out.append(part)
        out.append('</blockquote>' * depth)
        text = ''.join(out)

        text = text.strip('\n').replace('\n', '<br>\n')
        text = re.sub(r'(<br>\n)*(</?blockquote>)(<br>\n)*', r'\2', text)

        return re.sub(r'\x00(\d+)\x00', lambda m: slots[int(m.group(1))], text)


# --- Seitenrahmen ------------------------------------------------------------

def load_chrome():
    """Holt Kopf und Fuß der neuen Seite und passt die Pfade für forum/ an."""
    page = urllib.request.urlopen(SITE_URL, timeout=20).read().decode('utf-8')
    head = page[:page.index('<section class="page-head">')]
    foot = page[page.index('</main>'):]

    def fix(fragment):
        fragment = fragment.replace('href="index.php', 'href="../index.php')
        fragment = fragment.replace('href="assets/', 'href="../assets/').replace('src="assets/', 'src="../assets/')
        fragment = fragment.replace('href="forum/', 'href="../forum/')
        return fragment

    head = re.sub(r'<title>.*?</title>', '<title>{{TITLE}}</title>', fix(head), flags=re.S)
    head = re.sub(r'<meta name="description"[^>]*>', '<meta name="description" content="{{DESC}}">', head)
    return head, fix(foot)


def render(head, foot, title, h1, crumbs, body, desc='Archiv des Nebelland-Forums (2004–2007).'):
    crumb_html = '<nav class="crumbs" aria-label="Pfad">'
    parts = []
    for label, href in crumbs:
        parts.append(f'<a href="{href}">{esc(label)}</a>' if href else f'<span>{esc(label)}</span>')
    crumb_html += '<span aria-hidden="true">›</span>'.join(parts) + '</nav>'
    page_head = ('<section class="page-head"><div class="page-head__fog" aria-hidden="true"></div>'
                 '<div class="page-head__inner"><p class="eyebrow">Forum-Archiv</p>'
                 f'<h1>{esc(h1)}</h1></div></section>\n<main id="inhalt" class="site-main">\n')
    out = head.replace('{{TITLE}}', esc(title) + ' · Nebelland-Forum').replace('{{DESC}}', esc(desc))
    return out + page_head + crumb_html + body + '\n' + foot


def pagination(prefix, ident, current, pages):
    if pages <= 1:
        return ''
    items = []
    for p in range(1, pages + 1):
        if p == current:
            items.append(f'<span aria-current="page">{p}</span>')
        elif p in (1, pages) or abs(p - current) <= 2:
            items.append(f'<a href="{page_name(prefix, ident, p)}">{p}</a>')
        elif items and items[-1] != '<span>…</span>':
            items.append('<span>…</span>')
    return '<nav class="pagination" aria-label="Seiten">' + ''.join(items) + '</nav>'


# --- Hauptprogramm -----------------------------------------------------------

def main():
    print('Lese Struktur …')
    categories = query('SELECT categoryid, categoryname FROM tb_category ORDER BY categoryorder')
    boards = query(f"""
        SELECT b.boardid, b.boardname, b.boarddescription, b.categoryid
        FROM tb_board b
        WHERE COALESCE((SELECT LEFT(accessmask, 1) FROM tb_groupboard g
                        WHERE g.boardid = b.boardid AND g.groupid = {GUEST_GROUP}),
                       (SELECT LEFT(accessmask, 1) FROM tb_group WHERE groupid = {GUEST_GROUP})) = '1'
        ORDER BY b.boardorder""")
    board_ids = [int(b['boardid']) for b in boards]
    id_list = ','.join(map(str, board_ids))

    threads = query(f"""
        SELECT threadid, threadtopic, threadauthor, threadreplies, boardid, threadtime,
               threadlastreplyby, threadclosed, threadtop, threadcreationtime
        FROM tb_thread WHERE boardid IN ({id_list}) AND threadlink = 0""")
    thread_ids = {int(t['threadid']) for t in threads}

    print('Lese Beiträge …')
    posts = query(f"""
        SELECT p.postid, p.posttime, p.posttext, p.userid, p.postguestname, p.threadid,
               p.postlasteditby, p.postlastedittime
        FROM tb_post p JOIN tb_thread t ON t.threadid = p.threadid
        WHERE t.boardid IN ({id_list}) AND t.threadlink = 0
        ORDER BY p.posttime, p.postid""")
    users = {int(u['userid']): u for u in query('SELECT userid, username, usertitle, userposts FROM tb_user')}

    print('Repariere Umlaute …')
    pages_dir = os.path.join(ROOT, 'neu', 'pages')
    extra = []
    for dirpath, _, files in os.walk(pages_dir):
        for name in files:
            if name.endswith('.php'):
                with open(os.path.join(dirpath, name), encoding='utf-8') as f:
                    extra.append(f.read())
    extra += [p['posttext'] for p in posts if p['posttext'] and '?' not in p['posttext']]
    repair = UmlautRepair(os.path.join(ROOT, 'tools', 'de_wortliste.txt'), extra)
    for p in posts:
        p['posttext'] = repair.fix(p['posttext'])
        p['postguestname'] = repair.fix(p['postguestname'])
        p['postlasteditby'] = repair.fix(p['postlasteditby'])
    for t in threads:
        t['threadtopic'] = repair.fix(t['threadtopic'])
        t['threadauthor'] = repair.fix(t['threadauthor'])
    for b in boards:
        b['boardname'] = repair.fix(b['boardname'])
        b['boarddescription'] = repair.fix(b['boarddescription'])
    for u in users.values():
        u['username'] = repair.fix(u['username'])
        u['usertitle'] = repair.fix(u['usertitle'])
    print('  ', repair.stats)

    posts_by_thread = {}
    for p in posts:
        posts_by_thread.setdefault(int(p['threadid']), []).append(p)

    # Threads ohne Beiträge verwerfen, Kennzahlen aus den echten Daten berechnen
    threads = [t for t in threads if int(t['threadid']) in posts_by_thread]
    for t in threads:
        plist = posts_by_thread[int(t['threadid'])]
        t['_count'] = len(plist)
        t['_last'] = int(plist[-1]['posttime'])
        t['_first'] = int(plist[0]['posttime'])
        t['_lastby'] = author_name(plist[-1], users)
    thread_ids = {int(t['threadid']) for t in threads}

    threads_by_board = {}
    for t in threads:
        threads_by_board.setdefault(int(t['boardid']), []).append(t)
    for lst in threads_by_board.values():
        lst.sort(key=lambda t: (-int(t['threadtop'] or 0), -t['_last']))

    conv = Converter(thread_ids)
    head, foot = load_chrome()

    # Ausgabeordner neu anlegen
    if os.path.isdir(OUT):
        shutil.rmtree(OUT)
    os.makedirs(OUT)

    board_by_id = {int(b['boardid']): b for b in boards}

    # --- Übersicht ---
    total_posts = sum(t['_count'] for t in threads)
    body = ['<div class="ui-text center mb-2"><p>Das Forum war von 2004 bis 2007 der Treffpunkt der Nebelland-Gemeinschaft. '
            'Hier findest du alle öffentlichen Bereiche als schreibgeschütztes Archiv: '
            f'{plural(len(threads), "Thema", "Themen")} mit {plural(total_posts, "Beitrag", "Beiträgen")}.</p>'
            '<p class="muted">Interne Bereiche des Teams, private Nachrichten und persönliche Daten sind nicht enthalten.</p></div>',
            '<div class="thread-search"><label class="visually-hidden" for="themensuche">Themen durchsuchen</label>'
            '<input class="input" id="themensuche" type="search" placeholder="Themen nach Titel durchsuchen …" autocomplete="off">'
            '<ul id="treffer" aria-live="polite"></ul></div>']
    for cat in categories:
        cboards = [b for b in boards if b['categoryid'] == cat['categoryid'] and threads_by_board.get(int(b['boardid']))]
        if not cboards:
            continue
        body.append(f'<section class="forum-cat"><h2>{esc(cat["categoryname"])}</h2><ul class="forum-list">')
        for b in cboards:
            bt = threads_by_board[int(b['boardid'])]
            last = max(bt, key=lambda t: t['_last'])
            desc = conv.convert(html.unescape(re.sub(r'<[^>]+>', '', b['boarddescription']))) if b['boarddescription'] else ''
            body.append(
                f'<li><div><a class="forum-list__title" href="b{b["boardid"]}.html">{esc(b["boardname"])}</a>'
                + (f'<div class="forum-list__desc">{desc}</div>' if desc else '') +
                f'</div><div class="forum-list__meta"><strong>{plural(len(bt), "Thema", "Themen")}</strong> · '
                f'{plural(sum(t["_count"] for t in bt), "Beitrag", "Beiträge")}<br>zuletzt {fmt_day(last["_last"])}</div></li>')
        body.append('</ul></section>')

    search_data = [[int(t['threadid']), t['threadtopic'], board_by_id[int(t['boardid'])]['boardname']] for t in threads]
    with open(os.path.join(OUT, 'themen.json'), 'w', encoding='utf-8') as f:
        json.dump(search_data, f, ensure_ascii=False, separators=(',', ':'))
    body.append(SEARCH_SCRIPT)

    write('index.html', render(head, foot, 'Übersicht', 'Das Nebelland-Forum', [('Startseite', '../index.php'), ('Forum-Archiv', None)], ''.join(body)))

    # --- Bretter ---
    for b in boards:
        bid = int(b['boardid'])
        bt = threads_by_board.get(bid, [])
        if not bt:
            continue
        pages = math.ceil(len(bt) / THREADS_PER_PAGE)
        for page in range(1, pages + 1):
            chunk = bt[(page - 1) * THREADS_PER_PAGE: page * THREADS_PER_PAGE]
            rows = ['<section class="forum-cat"><ul class="forum-list">']
            for t in chunk:
                tid = int(t['threadid'])
                tpages = math.ceil(t['_count'] / POSTS_PER_PAGE)
                pin = '<span class="pin">Wichtig</span>' if int(t['threadtop'] or 0) else ''
                rows.append(
                    f'<li><div>{pin}<a class="forum-list__title" href="t{tid}.html">{esc(t["threadtopic"])}</a>'
                    f'<div class="forum-list__desc">von {esc(t["threadauthor"])} · {fmt_day(t["_first"])}'
                    + (f' · <a href="{page_name("t", tid, tpages)}">letzte Seite</a>' if tpages > 1 else '') +
                    f'</div></div><div class="forum-list__meta"><strong>{plural(t["_count"] - 1, "Antwort", "Antworten")}</strong><br>'
                    f'{fmt_day(t["_last"])} von {esc(t["_lastby"])}</div></li>')
            rows.append('</ul></section>')
            rows.append(pagination('b', bid, page, pages))
            title = b['boardname'] + (f' – Seite {page}' if page > 1 else '')
            write(page_name('b', bid, page), render(head, foot, title, b['boardname'],
                  [('Startseite', '../index.php'), ('Forum-Archiv', 'index.html'), (b['boardname'], None)], ''.join(rows)))

    # --- Themen ---
    print('Schreibe Themen …')
    for t in threads:
        tid = int(t['threadid'])
        b = board_by_id[int(t['boardid'])]
        plist = posts_by_thread[tid]
        pages = math.ceil(len(plist) / POSTS_PER_PAGE)
        for page in range(1, pages + 1):
            chunk = plist[(page - 1) * POSTS_PER_PAGE: page * POSTS_PER_PAGE]
            items = [pagination('t', tid, page, pages), '<div class="posts">']
            for p in chunk:
                uid = int(p['userid'] or 0)
                user = users.get(uid)
                title = esc(user['usertitle']) if user and user.get('usertitle') else ''
                edit = ''
                if p.get('postlasteditby') and int(p.get('postlastedittime') or 0):
                    edit = f'<div class="post__edit">Zuletzt bearbeitet von {esc(p["postlasteditby"])} am {fmt_date(p["postlastedittime"])}</div>'
                items.append(
                    f'<article class="post" id="p{p["postid"]}"><div class="post__author"><strong>{esc(author_name(p, users))}</strong>'
                    + (f'<span>{title}</span>' if title else '') +
                    f'</div><div class="post__main"><div class="post__date"><time>{fmt_date(p["posttime"])}</time>'
                    f'<a href="#p{p["postid"]}" aria-label="Link zu diesem Beitrag">#</a></div>'
                    f'<div class="post__body">{conv.convert(p["posttext"])}</div>{edit}</div></article>')
            items.append('</div>')
            items.append(pagination('t', tid, page, pages))
            title = t['threadtopic'] + (f' – Seite {page}' if page > 1 else '')
            write(page_name('t', tid, page), render(head, foot, title, t['threadtopic'],
                  [('Startseite', '../index.php'), ('Forum-Archiv', 'index.html'), (b['boardname'], f'b{b["boardid"]}.html'), ('Thema', None)],
                  ''.join(items)))

    print(f'Fertig: {len(boards)} Bretter, {len(threads)} Themen, {total_posts} Beiträge → {OUT}')


def author_name(post, users):
    uid = int(post['userid'] or 0)
    if uid and uid in users:
        return users[uid]['username']
    return (post.get('postguestname') or 'Gast').lstrip('~') or 'Gast'


def write(name, content):
    with open(os.path.join(OUT, name), 'w', encoding='utf-8') as f:
        f.write(content)


SEARCH_SCRIPT = """
<script>
(function () {
  var input = document.getElementById('themensuche'), list = document.getElementById('treffer'), data = null;
  function esc(s) { return s.replace(/[&<>"]/g, function (c) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; }); }
  function run() {
    var q = input.value.trim().toLowerCase();
    if (q.length < 2) { list.innerHTML = ''; return; }
    var hits = data.filter(function (t) { return t[1].toLowerCase().indexOf(q) !== -1; }).slice(0, 50);
    list.innerHTML = hits.length ? hits.map(function (t) {
      return '<li><a href="t' + t[0] + '.html">' + esc(t[1]) + ' <small>· ' + esc(t[2]) + '</small></a></li>';
    }).join('') : '<li><a>Keine Treffer</a></li>';
  }
  input.addEventListener('input', function () {
    if (data) { run(); return; }
    fetch('themen.json').then(function (r) { return r.json(); }).then(function (d) { data = d; run(); });
  });
})();
</script>
"""

if __name__ == '__main__':
    sys.exit(main())
