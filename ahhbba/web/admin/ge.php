<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
    <HEAD>
        <TITLE></TITLE>
        <META http-equiv=Content-Type content="text/html; charset=utf-8">
        <STYLE type=text/css>BODY {
                PADDING-RIGHT: 0px; PADDING-LEFT: 0px; BACKGROUND: #80bdcb; PADDING-BOTTOM: 0px; MARGIN: 0px; CURSOR: e-resize; PADDING-TOP: 0px
            }
        </STYLE>

        <SCRIPT language=JavaScript type=text/javascript>
            <!--
        var pic = new Image();
            pic.src = "images/arrow_right.gif";

            function toggleMenu()
            {
                frmBody = parent.document.getElementById('frame-body');
                imgArrow = document.getElementById('img');

                if (frmBody.cols == "0, 10, *")
                {
                    frmBody.cols = "200, 10, *";
                    imgArrow.src = "images/arrow_left.gif";
                }
                else
                {
                    frmBody.cols = "0, 10, *";
                    imgArrow.src = "images/arrow_right.gif";
                }
            }

            var orgX = 0;
            document.onmousedown = function(e)
            {
                var evt = Utils.fixEvent(e);
                orgX = evt.clientX;

                if (Browser.isIE)
                    document.getElementById('tbl').setCapture();
            }

            document.onmouseup = function(e)
            {
                var evt = Utils.fixEvent(e);

                frmBody = parent.document.getElementById('frame-body');
                frmWidth = frmBody.cols.substr(0, frmBody.cols.indexOf(','));
                frmWidth = (parseInt(frmWidth) + (evt.clientX - orgX));

                frmBody.cols = frmWidth + ", 10, *";

                if (Browser.isIE)
                    document.releaseCapture();
            }

            var Browser = new Object();

            Browser.isMozilla = (typeof document.implementation != 'undefined') && (typeof document.implementation.createDocument != 'undefined') && (typeof HTMLDocument != 'undefined');
            Browser.isIE = window.ActiveXObject ? true : false;
            Browser.isFirefox = (navigator.userAgent.toLowerCase().indexOf("firefox") != -1);
            Browser.isSafari = (navigator.userAgent.toLowerCase().indexOf("safari") != -1);
            Browser.isOpera = (navigator.userAgent.toLowerCase().indexOf("opera") != -1);

            var Utils = new Object();

            Utils.fixEvent = function(e)
            {
                var evt = (typeof e == "undefined") ? window.event : e;
                return evt;
            }
//-->
        </SCRIPT>

        <META content="MSHTML 6.00.3790.4426" name=GENERATOR>
    </HEAD>
    <BODY onselect="return false;">
        <TABLE id=tbl height="100%" cellSpacing=0 cellPadding=0>
            <TBODY>
                <TR>
                    <TD><A href="javascript:toggleMenu();">
                            <IMG id=img height=30 src="images/arrow_left.gif" width=10 border=0>
                        </A>
                    </TD>
                </TR>
            </TBODY>
        </TABLE>
    </BODY>
</HTML>