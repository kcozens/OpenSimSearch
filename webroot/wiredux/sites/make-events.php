<?

if($_SESSION[USERID] == ""){

echo "<script language='javascript'>

<!--

window.location.href='index.php?page=home';

// -->

</script>";

}else{
?>
<script>
function checklength(obj,warning_div){
    var mlength=obj.getAttribute? parseInt(obj.getAttribute("maxlength")) : ""
    if (obj.getAttribute && obj.value.length>mlength){
        obj.blur();
        obj.value=obj.value.substring(0,mlength);
        document.getElementById(warning_div).innerHTML = "<span style='color:red;font-weight:bold;'>max. "+mlength+"</span>";
    }
    else {
        warning_div.innerHTML = "";
    }
        document.getElementById('counter').innerHTML =  obj.value.length;
}
</script>

<FORM METHOD="POST" ACTION="index.php?page=save-events">
<table border="0" cellpadding="2" cellspacing="0" width="100%">
        <tr>
                <td>Event Name:</td>
                <td><input type="text" id="event_name" name="event_name" size="35" value="" /></td>
        </tr>
        <tr><td colspan="2"><br/></td></tr>
        <tr>
                <td valign="top">Description:</td>
                <td>
                <textarea name="event_desc" rows="7" cols="" style="width:90%" wrap="virtual" maxlength="1024" onfocus="return checklength(this,'contact_max_warning')" onkeyup="return checklength(this,'contact_max_warning')"/></textarea><br/>
                </td>
        </tr>
        <tr>
                <td></td>
                <td>        <div>
                                <div >
                                        <em id="contact_max_warning">max. <strong>1024</strong> </em>

                                        Characters typed: <em id="counter"></em>
                                </div>
                        </div>
                </td>

        </tr>

        <tr><td colspan="2"><br/></td></tr>
        <tr>
                <td>Date:</td>
                <td>
                <table border="0" cellspacing="0" cellpadding="3">
                <tr>
                        <td>
                        <select name="event_day">

                                <option value="1" >01</option>

                                <option value="2" >02</option>

                                <option value="3" >03</option>

                                <option value="4" >04</option>

                                <option value="5" >05</option>

                                <option value="6" >06</option>

                                <option value="7" >07</option>

                                <option value="8" >08</option>

                                <option value="9" >09</option>

                                <option value="10" >10</option>

                                <option value="11" >11</option>

                                <option value="12" >12</option>

                                <option value="13" >13</option>

                                <option value="14" >14</option>

                                <option value="15" >15</option>

                                <option value="16" >16</option>

                                <option value="17" >17</option>

                                <option value="18" >18</option>

                                <option value="19" >19</option>

                                <option value="20" >20</option>

                                <option value="21" >21</option>

                                <option value="22" >22</option>

                                <option value="23" >23</option>

                                <option value="24" >24</option>

                                <option value="25" >25</option>

                                <option value="26" >26</option>

                                <option value="27" >27</option>

                                <option value="28" >28</option>

                                <option value="29" >29</option>

                                <option value="30" >30</option>

                                <option value="31" >31</option>
                                                </select>
                        </td>
                        <td>
                                                <select name="event_month">
                                                        <option value="1"  >January</option>
                                                        <option value="2"  >February</option>
                                                        <option value="3"  >March</option>
                                                        <option value="4"  >April</option>
                                                        <option value="5"  >May</option>
                                                        <option value="6"  >June</option>
                                                        <option value="7"  >July</option>
                                                        <option value="8"  >August</option>
                                                        <option value="9"  >September</option>
                                                        <option value="10" >October</option>
                                                        <option value="11" >November</option>
                                                        <option value="12" >December</option>
                                                </select>
                        </td>
                        <td>
                        <select name="event_year">

                                <option value="2008" >2008</option>
                                <option value="2009" >2009</option>
                                <option value="2010" >2010</option>
                                                </select>
                        </td>
                    </tr>
                </table>
                </td>
        </tr>
        <tr><td colspan="2"><br/></td></tr>
        <tr>
                <td>Starts at:</td>
                <td>
                <table border="0" cellspacing="0" cellpadding="3">
                <tr>
                        <td>
                                <select name="time_hour">
                                <option value="0" >12 am</option>
                                <option value="1" >1 am</option>
                                <option value="2" >2 am</option>
                                <option value="3" >3 am</option>
                                <option value="4" >4 am</option>
                                <option value="5" >5 am</option>
                                <option value="6" >6 am</option>
                                <option value="7" >7 am</option>
                                <option value="8" >8 am</option>
                                <option value="9" >9 am</option>
                                <option value="10" >10 am</option>
                                <option value="11" >11 am</option>
                                <option value="12" >12 pm</option>
                                <option value="13" >1 pm</option>
                                <option value="14" >2 pm</option>
                                <option value="15" >3 pm</option>
                                <option value="16" >4 pm</option>
                                <option value="17" >5 pm</option>
                                <option value="18" >6 pm</option>
                                <option value="19" >7 pm</option>
                                <option value="20" >8 pm</option>
                                <option value="21" >9 pm</option>
                                <option value="22" >10 pm</option>
                                <option value="23" >11 pm</option>
                        </select>
                        </td>
                        <td>
                        <select name="time_minutes" >
                                <option value="0" >: 00</option>
                                <option value="15" >: 15</option>
                                <option value="30" >: 30</option>
                                <option value="45" >: 45</option>
                        </select>
                                                </td>
                </tr>
                </table>
                </td>
        </tr>
        <tr>
                <td></td>
        </tr>
        <tr><td colspan="2"><br/></td></tr>
        <tr>
                <td>Duration:</td>
                <td>
                <table border="0" cellspacing="0" cellspadding="1">
                <tr>
                        <td>
                        <select name="duration">
                                                        <option value="10" >10 minutes</option>
                                                        <option value="15" >15 minutes</option>
                                                        <option value="20" >20 minutes</option>
                                                        <option value="25" >25 minutes</option>
                                                        <option value="30" >30 minutes</option>
                                                        <option value="45" >45 minutes</option>
                                                        <option value="60" >1 hour</option>
                                                        <option value="90" >1.5 hours</option>
                                                        <option value="120" >2 hours</option>
                                                        <option value="150" >2.5 hours</option>
                                                        <option value="180" >3 hours</option>
                                                </select>
                        </td>
                </tr>
                </table>
                </td>
        </tr>
        <tr><td colspan="2"><br/></td></tr>
        <tr>
                <td>Category:</td>
                <td>
                <select name="category">
                <option value="">- Select Category -</option>
                                        <option value="27"  >Arts and Culture</option>
                                        <option value="28"  >Charity/Support Groups</option>
                                        <option value="22"  >Commercial</option>
                                        <option value="18"  >Discussion</option>
                                        <option value="26"  >Education</option>
                                        <option value="24"  >Games/Contests</option>
                                        <option value="20"  >Live Music</option>
                                        <option value="29"  >Miscellaneous</option>
                                        <option value="23"  >Nightlife/Entertainment</option>
                                        <option value="25"  >Pageants</option>
                                        <option value="19"  >Sports</option>
                                </select>
                </td>
        </tr>
        <tr><td colspan="2"><br/></td></tr>
        <tr>
                <td nowrap>Cover Charge?</td>
                <td>
                <table border="0" cellspacing="0" cellpadding="3">
                <tr>
                        <td>
                        <select name="cover_charge">
                        <option value="Y" >Yes</option>
                        <option value="N" >No</option>
                        </select>
                        </td>
                        <td>Amount (L$):</td>
                        <td><input type="text" name="amount" size="3" maxlength="5" value="" /></td>
                </tr>
                </table>
                </td>
        </tr>
        <tr><td colspan="2"><br/></td></tr>
        <tr>
                <td>Location:</td>
                <td>
                <select name="parcel_chosen">
                <option>Pick a parcel</option>
				<?
                // Getting the regions you have access to
                $DbLink->query("SELECT regionUUID, parcelname, landingpoint FROM ossearch.allparcels WHERE owneruuid = '".$_SESSION[USERID]."'");
                while(list($regionUUID,$parcelname,$landingpoint) = $DbLink->next_record()){
                echo "<OPTION value = $landingpoint|$regionUUID>".$parcelname."</OPTION>";
                }
                ?>
                </select>

        </tr>
                <tr><td colspan=2><br/></td></tr>
            <tr>
                    <td>Mature Event?:</td>
                    <td>
                    <table border="0" cellspacing="0" cellspadding="1">
                    <tr>
                            <td><input type="checkbox" name="access" value="21"  /></td>
                            <td>Yes, this will be a mature event.</td>
                    </tr>
                    </table>
                    </td>
            </tr>

        <tr><td colspan="2"><br/></td></tr>
        <tr>
                <td>&nbsp;</td>
                <td><br/><input type="button" name="action" value="Save Event" onclick="form.submit()"/></td>
        </tr>
        </table>
        </form>
</div>
</td></tr>
</table>
</FORM>
<?
}
?>