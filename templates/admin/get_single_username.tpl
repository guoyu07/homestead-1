{START_FORM}
<div class="hms">
  <div class="box">
    <div class="box-title"> <h1>{TITLE}</h1> </div>
    <div class="box-content">
        {MENU_LINK}<br /><br />
        <!-- BEGIN error_msg -->
        <span class="error">{ERROR}<br/></span>
        <!-- END error_msg -->
        {MESSAGE}<br /><br />
        <table>
            <tr>
                <th align="left">ASU Email:</th><td>{USERNAME}@appstate.edu</td>
            </tr>
            <tr>
                <td align="left">{ENABLE_AUTOCOMPLETE_LABEL}</td><td>{ENABLE_AUTOCOMPLETE}</td>
            </tr>
        </table>
        <br /><br />
        {SUBMIT_BUTTON}
    </div>
  </div>
</div>
{END_FORM}
