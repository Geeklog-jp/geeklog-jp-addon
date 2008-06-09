CONST URL="{site_url}/nexflow/orchestrator.php"
CONST USER=""
CONST PASSWORD=""
CONST MESSAGES=0
CONST SLEEPTIME=2000 'sleep for 2 seconds
ON ERROR RESUME NEXT

set WshShell = WScript.CreateObject("WScript.Shell")
while true
	runOrchestrator()
	WScript.Sleep SLEEPTIME
wend

sub runOrchestrator
	set objHttp= CreateObject("Msxml2.ServerXMLHttp")
	objHttp.open "GET", url ,false ,USER,PASSWORD
	objHttp.send()
	str=objHttp.responseText
	if MESSAGES then msgbox str
end sub
