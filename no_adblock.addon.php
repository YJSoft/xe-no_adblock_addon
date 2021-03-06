<?php

if(!defined('__XE__'))	exit();


// 애드온 설정이 되어 있지 않으면 기본 설정을 적용합니다.
if(empty($addon_info->log) === true){
    $addon_info->log = 'N';
}
if(empty($addon_info->block_detect_action) === true)
{
    $addon_info->block_detect_action = 'none';
}

if(empty($addon_info->block_detect_action_var) === true)
{

    if($addon_info->block_detect_action === 'modal')
    {
        $addon_info->block_detect_action_var = '<p>본 사이트에서는 무료로 서비스를 제공하기 위해 광고를 이용합니다.</p> <p>광고차단기능을 해재해주시면 감사하겠습니다</p> <p> 광고차단기능을 비활성하신후 아래의 재접속 버튼을 누르시기 바랍니다.</p>';
    }
    elseif($addon_info->block_detect_action === 'none')
    {
        $addon_info->block_detect_action_var = '"none"';
    }
    elseif($addon_info->block_detect_action === 'custom')
    {
        $addon_info->block_detect_action_var = '"custom"';
    }

}


if ( $called_position === 'before_module_init'
    && ( isset($_SESSION['no_adblock_addon']['fake_url'])
            && Context::get('mid') ===  $_SESSION['no_adblock_addon']['fake_url']['mid']
            && Context::get('document_srl') ===  $_SESSION['no_adblock_addon']['fake_url']['document_srl']
            && Context::get('search_keyword') ===  $_SESSION['no_adblock_addon']['fake_url']['search_keyword']))
{
    require_once(_XE_PATH_.'addons/no_adblock/no_adblock.class.php');

    $no_adblock_class = new no_adblock_class($addon_info);
    $no_adblock_class->getTester();

}
elseif ($called_position === 'before_display_content')
{

    // 관리자는 항상 종료합니다.
    if(Context::get('is_logged') === true && Context::get('logged_info')->is_admin === 'Y')
    {
        return;
    }

    if(isset($_SESSION['no_adblock_addon']['fake_url']) === false)
    {
        require_once(_XE_PATH_.'addons/no_adblock/no_adblock.class.php');

        $no_adblock_class = new no_adblock_class($addon_info);
        $_SESSION['no_adblock_addon']['fake_url'] = $no_adblock_class->getFakeUrl();
    }

    $fake_url = $_SESSION['no_adblock_addon']['fake_url']['url'];

    // adblock script를 가져옵니다.
    $get_script = '<script src="'.$fake_url.'"></script>';
    Context::addHtmlFooter($get_script);

}