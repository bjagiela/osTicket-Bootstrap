<?php
if(!defined('OSTCLIENTINC')) die('Access Denied!');
$info=array();
if($thisclient && $thisclient->isValid()) {
    $info=array('name'=>$thisclient->getName(),
                'email'=>$thisclient->getEmail(),
                'phone'=>$thisclient->getPhoneNumber());
}

$info=($_POST && $errors)?Format::htmlchars($_POST):$info;

$form = null;
if (!$info['topicId']) {
    if (array_key_exists('topicId',$_GET) && preg_match('/^\d+$/',$_GET['topicId']) && Topic::lookup($_GET['topicId']))
        $info['topicId'] = intval($_GET['topicId']);
    else
        $info['topicId'] = $cfg->getDefaultTopicId();
}

$forms = array();
if ($info['topicId'] && ($topic=Topic::lookup($info['topicId']))) {
    foreach ($topic->getForms() as $F) {
        if (!$F->hasAnyVisibleFields())
            continue;
        if ($_POST) {
            $F = $F->instanciate();
            $F->isValidForClient();
        }
        $forms[] = $F->getForm();
    }
}

?>
<div class="container-xxl">
    <div class="py-4 px-4 glasscard bg-body-tertiary">
        <h4 class=""><i class="bi bi-file-earmark-plus"></i> <?php echo __('Open a New Ticket');?></h4>
        <p class="text-secondary"><?php echo __('Please fill in the form below to open a new ticket.');?></p>
        <hr>
        
        <form id="ticketForm" method="post" action="open.php" enctype="multipart/form-data">
        <?php csrf_token(); ?>
        <input type="hidden" name="a" value="open">
        <?php
            if (!$thisclient) {
                $uform = UserForm::getUserForm()->getForm($_POST);
                if ($_POST) $uform->isValid();
                $uform->render(array('staff' => false, 'mode' => 'create'));
            }
            else { ?>
            <div class="mb-0 ms-2"><?php echo __('Creator: ');?><span class="fw-bold"><?php echo Format::htmlchars($thisclient->getName()); ?></span></div>
            <div class="mb-3 ms-2"><?php echo __('E-mail: ');?><span class="fw-bold"><?php echo $thisclient->getEmail(); ?></span></div>
            <hr>
            <?php } ?>
        <table class="table table-light">
            <tbody>
            <tr>
                <td colspan="2">
                    <label for="topicId" class="form-label"><?php echo __('Help Topic'); ?></label>
                    <select required class="form-control w-auto" id="topicId" name="topicId" onchange="javascript:
                            var data = $(':input[name]', '#dynamic-form').serialize();
                            $.ajax(
                            'ajax.php/form/help-topic/' + this.value,
                            {
                                data: data,
                                dataType: 'json',
                                success: function(json) {
                                $('#dynamic-form').empty().append(json.html);
                                $(document.head).append(json.media);
                                }
                            });">
                        <option value="" selected="selected">&mdash; <?php echo __('Select a Help Topic');?> &mdash;</option>
                        <?php
                        if($topics=Topic::getPublicHelpTopics()) {
                            foreach($topics as $id =>$name) {
                                echo sprintf('<option value="%d" %s>%s</option>',
                                        $id, ($info['topicId']==$id)?'selected="selected"':'', $name);
                            }
                        } ?>
                    </select>
                    <font class="error">&nbsp;<?php echo $errors['topicId']; ?></font>
                </td>
            </tr>
            </tbody>
            <tbody id="dynamic-form">
                <?php
                $options = array('mode' => 'create');
                foreach ($forms as $form) {
                    include(CLIENTINC_DIR . 'templates/dynamic-form.tmpl.php');
                } ?>
            </tbody>
            <tbody>
            <?php
            if($cfg && $cfg->isCaptchaEnabled() && (!$thisclient || !$thisclient->isValid())) {
                if($_POST && $errors && !$errors['captcha'])
                    $errors['captcha']=__('Please re-enter the text again');
                ?>
            <tr class="captchaRow">
                <td class="required"><?php echo __('CAPTCHA Text');?>:</td>
                <td>
                    <span class="captcha"><img src="captcha.php" border="0" align="left"></span>
                    &nbsp;&nbsp;
                    <input id="captcha" type="text" name="captcha" size="6" autocomplete="off">
                    <em><?php echo __('Enter the text shown on the image.');?></em>
                    <font class="error">*&nbsp;<?php echo $errors['captcha']; ?></font>
                </td>
            </tr>
            <?php
            } ?>
            <tr><td colspan=2>&nbsp;</td></tr>
            </tbody>
        </table>
        <hr/>
        <p class="buttons" style="text-align:center;">
                <input class="btn btn-sm btn-primary" type="submit" value="<?php echo __('Create Ticket');?>">
                <input class="btn btn-sm btn-dark" type="reset" name="reset" value="<?php echo __('Reset');?>">
                <input class="btn btn-sm btn-dark" type="button" name="cancel" value="<?php echo __('Cancel'); ?>" onclick="javascript:
                    $('.richtext').each(function() {
                        var redactor = $(this).data('redactor');
                        if (redactor && redactor.opts.draftDelete)
                            redactor.plugin.draft.deleteDraft();
                    });
                    window.location.href='index.php';">
        </p>
        </form>
    </div>
</div>