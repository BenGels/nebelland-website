# Nebelland – Website

Website des deutschen **Ultima-Online-Freeshards „Nebelland“**, eines Rollenspiel-Servers
auf Basis von RunUO. Die Seite stammt **ursprünglich aus dem Jahr 2005** und wurde damals
auf einem klassischen XAMPP-Stack betrieben (Apache, PHP 4/5, MySQL; alles auf einer Maschine,
DB-Host `localhost`). Ab 2026 wurde sie restauriert, in Docker lauffähig gemacht und um eine
modernisierte Fassung unter `neu/` ergänzt.

> Dieses Repository ist in erster Linie ein **Archiv**. Inhalt und Code wurden weitgehend im
> Originalzustand belassen: PHP-4/5-Stil, `mysql_*`-Funktionen, Latin-1-Kodierung, Frames.

Zugehörige Repositories bzw. Verzeichnisse:

| Verzeichnis        | Inhalt |
|--------------------|--------|
| `../website`       | dieses Repo: Originalseite (2005 ff.) und neue Seite `neu/` |
| `../website-docker`| Docker-Image (PHP 5.6 + Apache), mit dem die Seite heute läuft |
| `../runuo`         | Spielserver (RunUO + Nebelland-Skripte), ebenfalls ab 2005 |

---

## Geschichte und Eckdaten

- **2005:** Start der Website, parallel zum Nebelland-Spielserver (RunUO).
- Domains im Lauf der Zeit: `nebelland.com` / `forum.nebelland.com`, `nebelland-server.de`, `uonl.de`.
  Zeitweise lief die Seite direkt über eine IP (`85.25.146.175`, siehe `Patches/konfiguration.xml`).
- Community-Kanal: IRC `#Nebelland` auf GameSurge.
- Foren: zuerst **ThWboard** (`thwb/`, Datenbank `apboard`), dazu ein stark angepasstes
  **phpBB 2** (`phpBB2/`) mit Charaktererstellung, Staff-Bewerbungen und Accountverwaltung.
  Der letzte vorhandene Forendump ist vom **04.12.2007** (`apboard_2007_12_04_12_25.sql`,
  wegen personenbezogener Daten nicht im Repo).
- Impressum (`sub/impressum.html`): Rechte an Ultima Online bei Origin; RunUO ist ein Fremdprodukt.
  Google Analytics (`UA-2379081-1`) war eingebunden.
- **2026:** Restaurierung, Docker-Betrieb, neue Oberfläche `neu/` und ein statisches,
  schreibgeschütztes Forum-Archiv (`neu/forum/`).

---

## Verzeichnisstruktur

| Pfad | Beschreibung |
|------|--------------|
| `index.php` | Einstieg der alten Seite. Inhalte kommen per `?s=<seite>` aus `sub/*.html` (`switch`), Forum und Staffintern als iFrames. |
| `sub/` | Inhaltsseiten: Nebelland, Geschichte, Rassen (Mensch, Zwerg, Licht-/Nachtelb), Klassen (Krieger, Magier, Handwerker), Städte (Dreibrücken, Halbmond, Morgenlicht, Gadarosch), Götter, Geister, Gilden, Regionen, Skills, Magie/Zauber, Regeln, FAQ, Anmeldung, Häuser, Status, „Ewiger Status“. |
| `sub/Buch/` | Bibliothek: Ingame-Bücher aus der RunUO-Datenbank. |
| `connect.php`, `sub/connect.php` | DB-Verbindung der alten Seite (Datenbank `hp`). |
| `bild.php`, `add_patch.php` | Hilfsskripte (Bilder, Patch-Einträge). |
| `Patches/` | Patch-Server für den Nebelland-Patcher: `index.php` liefert die Patchliste aus der DB, dazu die Patch-Archive (Karte, Art, Cliloc, Login-Screen …), `patch.xml`, `konfiguration.xml` (Patcher-Vorlage). |
| `downloads/` | Downloads: Patcher-Installer, `NeLa.exe`, Pandora's Box, RLT. Die EA-Client-Dateien (Muls, Clients) fehlen im Repo, siehe unten. |
| `NeLa-Patcher-Installation.exe` | Installer des Patchers (Java-Patcher, siehe `../java-patcher`, `../NL-Patcher`). |
| `thwb/` | ThWboard-Forum (Konfiguration: `thwb/inc/config.inc.php`). |
| `phpBB2/` | Angepasstes phpBB 2 (Konfiguration: `phpBB2/config.php`). |
| `!staff/`, `staff/` | Interner Staff-Bereich mit Accountliste, Hausverwaltung, To-do, MySQLDumper, Server-Altständen und Test-Patches. **Nicht im Repo**, nur lokal vorhanden. |
| `neu/` | **Neue Seite (2026)**, siehe unten. |
| `tools/build_forum_archive.py` | Erzeugt das statische Forum-Archiv `neu/forum/` aus der ThWboard-DB. |
| `tools/de_wortliste.txt` | Deutsche Wortliste (Hilfsdatei für das Archiv-Tool). |

### Neue Seite `neu/`

- `neu/index.php` ist ein Router (`?s=home`, `news`, `nebelland`, `anmeldung`, `regeln`, `faq`,
  `files`, `staff`, `gesch`, `rassen`, `klassen`, `staedte`, `goetter`, `gilden`, `regionen`, `stats`,
  `skills`, `magie`, `zauber`, `status`, `ewiger`, `haus`, `final` …) auf die Dateien in `neu/pages/`.
- `neu/inc/config.php` ist die zentrale Konfiguration: DB-Host `nela-mysql-restore`, Datenbanken
  `hp` (News, Anmeldungen, Häuser) und `runuo` (Spielerstatus, Ewiger Status, Bibliothek),
  IRC-Daten, Link auf den UO-Client (Mondain's Legacy), Patcher-URL.
- `neu/inc/db.php` ist eine schlanke DB-Schicht auf `mysql_*` (PHP 5.6), die bei Fehlern nicht abbricht.
- `neu/forum/` ist das statische HTML-Archiv des alten Forums. Übernommen wurden nur Bretter, die
  Gäste lesen durften, ohne PNs, E-Mail-Adressen, IPs oder Passwort-Hashes. **Das Archiv ist nicht
  im Repo**, weil es Beiträge der Community enthält. Es lässt sich mit `tools/build_forum_archive.py`
  aus der Datenbank neu erzeugen.
- Öffentlich (über Cloudflare Access) sind nur `/neu/…` und `/Patches/…` freigegeben
  (Policy `^/(neu|Patches)(/.*)?$`).

---

## Datenbanken

| Datenbank | Verwendet von |
|-----------|---------------|
| `hp` | alte und neue Seite: News, Anmeldungen, Häuser, Patches |
| `runuo` | wird vom Spielserver befüllt (`Scripts/Nebelland/Database.cs`); Spielerstatus, Ewiger Status, Bibliothek |
| `apboard` | ThWboard-Forum (Tabellenpräfix `tb_`) |
| `phpbb` | phpBB 2 (Tabellenpräfix `phpbb_`) |
| `dnwpatcher` | Patcher (wird in `website-docker/start.sh` angelegt) |

Die Tabellen sind **Latin-1**. Die neue Seite setzt `mysql_set_charset('utf8')`, damit MySQL beim
Auslesen konvertiert.

Heute laufen Website und RunUO gegen den gemeinsamen MySQL-Container **`nela-mysql-restore`**.

---

## Zugangsdaten / Secrets

Passwörter stehen **nicht** im Repository. Alle PHP-Dateien mit DB-Zugriff binden eine zentrale,
per `.gitignore` ausgeschlossene Datei ein:

```php
require_once dirname(__FILE__) . '/../secrets.php'; // Zugangsdaten (nicht im Git)
```

Einrichtung:

```sh
cp secrets.php.example secrets.php   # Passwörter eintragen
cp .env.example .env                 # nur für tools/build_forum_archive.py
```

| Konstante | Bedeutung |
|-----------|-----------|
| `NL_DB_PASS` | MySQL-root der alten Seite (`hp`, `phpbb`, `runuo`) |
| `NL_DB_PASS_ALT` | zweites Alt-Passwort (`connect.php`, `thwb`, `Patches/index.php`) |
| `NL_DB_PASS_NEU` | MySQL-root im Container `nela-mysql-restore` (neue Seite) |

Außerdem ausgeschlossen (siehe `.gitignore`):

- `.env*` (außer `.env.example`), TLS-Zertifikate und Schlüssel (`*.key`, `*.crt`, `*.pem` …,
  z. B. das Tailscale-Zertifikat `*.ts.net`), `.htpasswd`
- SQL-Dumps (`*.sql`) mit E-Mail-Adressen, IPs, Passwort-Hashes und PNs
- `privat/` (Export privater Forennachrichten)
- `Patches/Saves.rar`, `Patches/Nebelland.rar`, `!staff/Nebelland2.rar`: RunUO-Spielstände mit
  `accounts.xml` (Accountnamen und Passwort-Hashes)
- `!staff/Saves/`, `!staff/Logs/`, `!staff/mysqldumper/work/`
- **Nicht veröffentlicht, aber lokal vorhanden:**
  - der Staff-Bereich `!staff/` und `staff/`
  - das Forum-Archiv `neu/forum/`
  - EA/Origin-Client-Dateien: `downloads/Muls.rar`, `downloads/OSI.rar`, die selbstentpackenden
    Mul-Pakete `downloads/art.exe`, `st.exe` und `uonl.exe`, die gepatchten Clients
    `downloads/nl_client.exe` und `Patches/nl_test.exe`, `Patches/Muls.rar` sowie die Patches
    `Patches/alchemie_cliloc.zip` (enthält einen Client) und `Patches/skillsundclient.zip`
    (enthält EA-Fonts), dazu generell `*.mul`

  Für einen vollständigen Betrieb müssen diese Dateien aus dem Backup danebengelegt werden. Ohne
  diese beiden Patches funktioniert die Patchkette für neue Spieler nicht.
- macOS-/Windows-Metadaten (`.AppleDouble/`, `.DS_Store`, `Thumbs.db`), Editor- und Claude-Code-Dateien

> **Hinweis:** Die Passwörter der Originalseite waren jahrelang im Klartext im Code. Wer sie
> irgendwo weiterverwendet, sollte sie ändern.

---

## Betrieb

Die Seite läuft über das Image aus `../website-docker` (PHP 5.6 + Apache). Dort liegt unter
`webroot/` eine angepasste Kopie dieser Seite (DB-Host `nela-mysql-restore` statt `localhost`).
`neu/` wird aus diesem Repo per Volume eingebunden:

```yaml
volumes:
  - ../website/neu:/var/www/html/neu:ro
```

Details stehen in `../website-docker/README.md`.

### Forum-Archiv neu erzeugen

```sh
cp .env.example .env    # NL_DB_PASS usw. setzen
set -a; . ./.env; set +a
python3 tools/build_forum_archive.py
```

Das Skript liest per `docker exec <NL_DB_CONTAINER> mysql …` aus der Datenbank `apboard`.
Seitenkopf und -fuß holt es von der laufenden neuen Seite (`NL_SITE_URL`) und schreibt nach `neu/forum/`.

---

## Rechtliches

Ultima Online und die zugehörigen Marken gehören Electronic Arts / Origin. RunUO ist ein eigenständiges
Open-Source-Projekt. Texte, Grafiken und Skripte von Nebelland gehören ihren jeweiligen Autorinnen
und Autoren (siehe `sub/impressum.html`).
