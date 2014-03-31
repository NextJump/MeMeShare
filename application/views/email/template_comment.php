<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>New Interaction Posted</title>
        <style type="text/css">
            /* Client-specific Styles */
            #outlook a{padding:0;} /* Force Outlook to provide a "view in browser" button. */
            body{width:100% !important;} .ReadMsgBody{width:100%;} .ExternalClass{width:100%;} /* Force Hotmail to display emails at full width */
            body{-webkit-text-size-adjust:none;} /* Prevent Webkit platforms from changing default text sizes. */
            
            /* Reset Styles */
            body{margin:0; padding:0;}
            img{border:0; height:auto; line-height:100%; outline:none; text-decoration:none;}
            table td{border-collapse:collapse;}
            
            /* Template Styles */

            body {
                background-color: #FFFFFF;
                font-family: 'Arial', sans-serif;
                font-size: 13px;
                color: #868974;
            }

            #backgroundTable {
                margin-top: 10px;
                color: #868974;
            }

            h1{
                display:block;
                margin-top:2%;
                margin-right:0;
                margin-bottom:1%;
                margin-left:0;
            }

            h2{
                display:block;
                margin-top:2%;
                margin-right:0;
                margin-bottom:1%;
                margin-left:0;
            }

            h3{
                display:block;
                margin-top:2%;
                margin-right:0;
                margin-bottom:1%;
                margin-left:0;
            }

            h4{
                display:block;
                margin-top:2%;
                margin-right:0;
                margin-bottom:1%;
                margin-left:0;
            }
            
            img{
                display:inline;
                height:auto;
            }

            #content{
                -webkit-box-shadow: 0px 1px 2px #d1d4be;
                box-shadow: 0px 1px 2px #d1d4be;
                border: 1px solid #d1d4be;
            }
            
        </style>
    </head>
    <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
        <center>
            <table bgcolor="#E7EAD7" border="0" cellpadding="0" cellspacing="0" width="700" id="backgroundTable">
                <tr>
                    <td colspan='3' height='50'></td>
                </tr>
                <tr>
                    <td width='70'></td>
                    <td>
                        <table border="0" cellpadding="0" cellspacing="0" width="100%"> 
                            <?php // Header/Logo section?>
                            <tr>
                                <td align='left' valign='top' width='100%' height='50'>
                                    <img src="<?=$logoImg?>" />
                                </td>
                            </tr>

                            <tr>
                                <td height='15'></td>
                            </tr>

                            <?php // Content?>
                            <tr>
                                <td align='left' valign='top'>
                                    <table bgcolor="#FFFFFF" border="0" cellpadding="20" cellspacing="0" width="100%" id="content">
                                        <tr>
                                            <td width="25%" align='left' valign='top'>
                                                <img id="posterImg" src="<?=$commentorImgUrl?>" width="120" />
                                            </td>
                                            <td id="interaction" align='left' valign='top'>
                                                <b style="color: #868974;">Hi&nbsp;<?=$yourFname?>,</b>
                                                <p style="color: #868974;">
                                                    <b style="color:#f38630;font-weight:normal;"><?=$commentorFname?>&nbsp;<?=$commentorLname?></b> just commented on your interaction:
                                                </p>
                                                <table border="0" cellpadding="0" cellspacing="0" width="100%" id="description">
                                                    <tr>
                                                        <td width="9%" align='left' valign='top'>
                                                            <img src="<?=$baseUrl?>assets/img/quote.gif" width="26" />
                                                        </td>
                                                        <td align='left' valign='top'>
                                                            <b style="color: #868974;"><?=$commentText?></b>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td height='20'></td>
                                                    </tr>
                                                </table>
                                                <table bgcolor="#f38630" border="0" cellpadding="10" cellspacing="0" width="220" id="seePostButton">
                                                    <tr>
                                                        <td style="text-align:center;font-weight:bold;font-size:110%;"align='left' valign='top'>
                                                            <a style="color:#FFFFFF;text-decoration:none;" href="<?=$linkUrl?>">
                                                                VIEW INTERACTION
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width='70'></td>
                </tr>
                <tr>
                    <td colspan='3' height='50'></td>
                </tr>
            </table>
        </center>
    </body>
</html>