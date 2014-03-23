<h2>wpConfigure Administration</h2>
<?php
if(function_exists('wpConfigure')) {
    $aSiteConfig = wpConfigure('site');
    if(is_array($aSiteConfig)) {
        echo 'wpConfigure looks properly configured!';
        return;
    }
}
?>
<h3>Manual Confinguration</h3>
<p>
Your wp-config.php file was not automatically found during activation of wpConfigure.
Backup your wp-config.php file and paste the contents of the code below to start using wpConfigure.
</p>
<?php
$aConfigInfo = wpConfigureFindConfig();
ob_start();
echo '<?php' . PHP_EOL;
require __DIR__ . '/wp-config-sample.php';
highlight_string(ob_get_clean());
return;
?>
<h3>Plugin Config</h3>
<strong>Production/Staging Configuration</strong>
<br/>
<?php
highlight_string(wpConfigureStub('plugin', 'wpConfigure', 'main'));
?>
<br/>
<br/>
<strong>Development Configuration</strong>
<br/>
<?php
highlight_string(wpConfigureStub('plugin', 'wpConfigure', 'local'));
?>
<h3>Theme Config</h3>
<strong>Production/Staging Configuration</strong>
<br/>
<?php
highlight_string(wpConfigureStub('theme', 'wpConfigure', 'main'));
?>
<br/>
<br/>
<strong>Development Configuration</strong>
<br/>
<?php
highlight_string(wpConfigureStub('theme', 'wpConfigure', 'local'));
?>