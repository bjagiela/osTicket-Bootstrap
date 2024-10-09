<?php
if(!defined('OSTCLIENTINC')) die('Access Denied');

$email=Format::input($_POST['luser']?:$_GET['e']);
$passwd=Format::input($_POST['lpasswd']?:$_GET['t']);

$content = Page::lookupByType('banner-client');

// if ($content) {
//     list($title, $body) = $ost->replaceTemplateVariables(
//         array($content->getLocalName(), $content->getLocalBody()));
// } else {
//     $title = __('Sign In');
//     $body = __('To better serve you, we encourage our clients to register for an account and verify the email address we have on record.');
// }

$title = __('Sign In');
$body = __('To better serve you, we encourage our clients to register for an account and verify the email address we have on record.');

?>
<div class="container mt-5 mt-auto mb-auto">
    <!-- <p><?php echo Format::display($body); ?></p> -->
    <form action="login.php" method="post" id="clientLogin">
        <?php csrf_token(); ?>
        <div class="row justify-content-center align-content-center gy-4">
            <div class="col-12 col-xl-4 col-lg-4 col-md-6 h-100">
                <div class="px-4 py-4 card bg-light-subtle shadow h-100 align-content-center">
                    <img class="img-fluid mb-3" src="/logo.php" alt="osTicket Boostrap">
                    <h4 class="text-center fw-semibold mb-4"><i class="bi bi-door-open"></i>  <?php echo Format::display($title); ?></h4>
                    <strong><?php echo Format::htmlchars($errors['login']); ?></strong>
                    <div class="mb-3 form-floating"> 
                        <input id="username" type="text" placeholder="<?php echo __('Email or Username'); ?>" name="luser"  value="<?php echo $email; ?>" class="form-control">
                        <label for="username"><?php echo __('Email or Username'); ?></label>
                    </div>
                    <div class="mb-3 form-floating">
                        <input id="passwd" type="password" placeholder="<?php echo __('Password'); ?>" name="lpasswd" maxlength="128" value="<?php echo $passwd; ?>" class="form-control">
                        <label for="passwd"><?php echo __('Password'); ?></label>
                    </div>
                    <div class="text-center">
                        <button class="btn btn-primary w-100" type="submit"><?php echo __('Sign In'); ?></button>
                        <?php if ($suggest_pwreset) { ?>
                                <a href="pwreset.php" class="d-block mt-2"><?php echo __('Forgot My Password'); ?></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-xl-3 col-lg-4 col-md-6">
                <div class="px-4 py-4 card bg-light-subtle shadow h-100 justify-content-around text-light-emphasis">
                    <?php
                    $ext_bks = array();
                    foreach (UserAuthenticationBackend::allRegistered() as $bk)
                        if ($bk instanceof ExternalAuthentication)
                            $ext_bks[] = $bk;

                    if (count($ext_bks)) {
                        foreach ($ext_bks as $bk) { ?>
                    <div class="external-auth"><?php $bk->renderExternalLink(); ?></div><?php
                        }
                    }
                    if ($cfg && $cfg->isClientRegistrationEnabled()) {
                        if (count($ext_bks)) echo '<hr style="width:70%"/>'; ?>
                        <div class="text-center py-2">
                            <div><?php echo __('Not yet registered?'); ?></div>
                            <a href="account.php?do=create" class="btn btn-sm btn-outline-primary mt-1 w-100"><i class="bi bi-person-add"></i> <?php echo __('Create an account'); ?></a>
                        </div>
                    <?php } ?>
                    <div class="text-center py-2">
                        <?php
                        if ($cfg->getClientRegistrationMode() != 'disabled'
                            || !$cfg->isClientLoginRequired()) {
                            echo sprintf(__('If this is your first time contacting us or you\'ve lost the ticket number, please %s open a new ticket %s'),
                                '<div><a href="open.php" class="btn btn-sm btn-outline-primary mt-1 w-100"><i class="bi bi-ticket-perforated"></i> ', '</a></div>');
                        } ?>
                    </div>
                    <div class="text-center py-2">
                        <?php echo __("I'm an agent"); ?>
                        <div><a href="<?php echo ROOT_PATH; ?>scp/" class="btn btn-sm btn-outline-primary mt-1 w-100"><i class="bi bi-wrench"></i> <?php echo __('sign in here'); ?></a></div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
