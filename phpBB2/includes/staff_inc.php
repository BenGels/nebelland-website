<?
$staff_groupid = 7;

/*  Get sure to show staffler group this page only
*/
function CheckStaffler($user_id, $staff_groupid)
{
    global $db;
    $sql = "SELECT group_id
      FROM " . USER_GROUP_TABLE . "
      WHERE user_id=$user_id";
    $result = $db->sql_query($sql);
    $staff = false;
    while($row = $db->sql_fetchrow($result))
    {
      if($row['group_id'] == $staff_groupid)
      {
        $staff = true;
      }
    }
    return $staff;
}
?>