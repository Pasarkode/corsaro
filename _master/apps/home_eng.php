<?php
if(isset($_GET["project"]))
    $progetto=strtoupper($_GET["project"]);
else
    $progetto="";
?>
<!DOCTYPE html>

<html>
<head>

    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge, chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Home Project <?php print $progetto ?></title>

    <style>
    body{font-family:sans-serif;font-size:12px;}
    a{color: maroon;text-decoration:none;font-weight:bold;}
    a:hover{text-decoration:underline;}
    table{font-family:sans-serif;font-size:12px;}
    #title{position:absolute;left:50px;top:40px;font-size:24px;}
    #frame{position:absolute;left:50px;top:120px;font-size:18px;}
    .changelang{padding:2px;border:1px solid silver;background:#F0F0F0;}
    </style>

</head>

<body>

<div style="position:absolute;left:700px;">
<a href="home.php?project=<?php print $progetto ?>&lang=italiano" class="changelang">Italiano</a>
</div>

<div id="title">
Home Project <?php print $progetto ?>
</div>

<div id="frame">

<b>Step 0)</b> Diagnostics:
<a href="../cambusa/sysinstall/sysinfo.php" target="_blank">Show system</a> or <a href="../cambusa/sysinstall/sysdiagnostics.php" target="_blank">Execute checks</a><br>
<blockquote>
<span style="font-size:14px;">
Highlights the system infos and executes some tests.
</span>
</blockquote>
<br>

<b>Step 1)</b> System initialization:
<a href="../cambusa/sysinstall/sysinstall.php?project=<?php print strtolower($progetto) ?>" target="_blank">Monad, Ego, Pulse and dictionaries</a><br>
<blockquote>
<span style="font-size:14px;">
<i>Ego</i> database is initialized with an administrator user:<br>
&nbsp;&nbsp;&nbsp;user <i>demiurge</i><br>
&nbsp;&nbsp;&nbsp;password <i>sonoio</i><br>
The password must be changed on first use.
</span>
</blockquote>
<br>

<b>Step 2)</b> Users and permissions:
<a href="../cambusa/ryego/ryego.php" target="_blank">Ego</a><br>
<blockquote>
<span style="font-size:14px;">
You change the password and fit users and permissions.
</span>
</blockquote>
<br>

<b>Step 3)</b> Structuring database:
<a href="../cambusa/rymaestro/rymaestro.php" target="_blank">Maestro</a><br>
<blockquote>
<span style="font-size:14px;">
Under section <i>Upgrade</i>
update the database [<i><?php print strtolower($progetto) ?></i>].
</span>
</blockquote>
<br>

<b>Step 4)</b> Using software:
<a href="../apps/corsaro/corsaro.php" target="_blank">Corsaro</a><br>
<blockquote>
<span style="font-size:14px;">
On first use, or after a database update, open<br>
&nbsp;&nbsp;&nbsp;<i>Maintenance (folder) -> Options (item) -> Maintenance (tab)</i><br>
and click<br>
&nbsp;&nbsp;&nbsp;<i>Refresh all views</i>.
</span>
</blockquote>
<br>

</div>

</body>
</html>
