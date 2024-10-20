<?php
if(!defined('OSTCLIENTINC') || !$thisclient || !$ticket || !$ticket->checkUserAccess($thisclient)) die('Access Denied!');

$info=($_POST && $errors)?Format::htmlchars($_POST):array();

$type = array('type' => 'viewed');
Signal::send('object.view', $ticket, $type);

$dept = $ticket->getDept();

if ($ticket->isClosed() && !$ticket->isReopenable())
    $warn = sprintf(__('%s is marked as closed and cannot be reopened.'), __('This ticket'));

//Making sure we don't leak out internal dept names
if(!$dept || !$dept->isPublic())
    $dept = $cfg->getDefaultDept(); ?>

<div class="container-xxl">
    <div class="py-4 px-4 bg-body-tertiary glasscard">
        <?php
        if ($thisclient && $thisclient->isGuest()
            && $cfg->isClientRegistrationEnabled()) { ?>

        <div id="msg_info">
            <i class="icon-compass icon-2x pull-left"></i>
            <strong><?php echo __('Looking for your other tickets?'); ?></strong><br />
            <a href="<?php echo ROOT_PATH; ?>login.php?e=<?php
                echo urlencode($thisclient->getEmail());
            ?>" style="text-decoration:underline"><?php echo __('Sign In'); ?></a>
            <?php echo sprintf(__('or %s register for an account %s for the best experience on our help desk.'),
                '<a href="account.php?do=create" style="text-decoration:underline">','</a>'); ?>
        </div>

        <?php } ?>

        <div class="row align-items-center mb-3">
            <div class="col-12 col-md-7">
                <div class="text-md-start text-center">
                    <h3 class="d-block d-md-inline mb-2 mb-md-0">
                        <a href="<?php echo Http::refresh_url(); ?>" class="me-2 badge text-bg-dark text-decoration-none shadow-sm">
                            #<?php echo $ticket->getNumber(); ?>
                        </a>
                    </h3>
                    <h4 class="d-block d-md-inline">
                        <?php 
                            $subject_field = TicketForm::getInstance()->getField('subject');
                            echo $subject_field->display($ticket->getSubject()); 
                        ?>
                    </h4>
                </div>
            </div>
            <div class="col-12 col-md-5 text-md-end text-center mt-md-0 mt-sm-2 mt-2">
                <a class="btn btn-sm btn-outline-dark shadow-sm" href="tickets.php?a=print&id=<?php echo $ticket->getId(); ?>">
                    <i class="bi bi-printer"></i>
                    <?php echo __('Print'); ?>
                </a>
                <?php if ($ticket->hasClientEditableFields()
                    // Only ticket owners can edit the ticket details (and other forms)
                    && $thisclient->getId() == $ticket->getUserId()) { ?>
                        <a class="btn btn-sm btn-outline-dark shadow-sm" href="tickets.php?a=edit&id=<?php echo $ticket->getId(); ?>"><i class="bi bi-pen"></i> <?php echo __('Edit'); ?></a>
                <?php } ?>
            </div>
        </div>
        <!-- <hr> -->

        <div class="row gy-3">
            <div class="col-12 col-lg-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-header">
                        <?php echo __('Basic Ticket Information'); ?>
                    </div>
                    <div class="card-body py-2">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <th><?php echo __('Ticket Status');?>:</th>
                                <td><?php echo ($S = $ticket->getStatus()) ? $S->getLocalName() : ''; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo __('Department');?>:</th>
                                <td><?php echo Format::htmlchars($dept instanceof Dept ? $dept->getName() : ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo __('Create Date');?>:</th>
                                <td><?php echo Format::datetime($ticket->getCreateDate()); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-header">
                        <?php echo __('User Information'); ?>
                    </div>
                    <div class="card-body py-2">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <th width="100"><?php echo __('Name');?>:</th>
                                <td><?php echo mb_convert_case(Format::htmlchars($ticket->getName()), MB_CASE_TITLE); ?></td>
                            </tr>
                            <tr>
                                <th width="100"><?php echo __('Email');?>:</th>
                                <td><?php echo Format::htmlchars($ticket->getEmail()); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo __('Phone');?>:</th>
                                <td><?php echo $ticket->getPhoneNumber(); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <table cellpadding="1" cellspacing="0" border="0" id="ticketInfo">
            <tr>
                <td colspan="2">
        <!-- Custom Data -->
        <?php
        $sections = $forms = array();
        foreach (DynamicFormEntry::forTicket($ticket->getId()) as $i=>$form) {
            // Skip core fields shown earlier in the ticket view
            $answers = $form->getAnswers()->exclude(Q::any(array(
                'field__flags__hasbit' => DynamicFormField::FLAG_EXT_STORED,
                'field__name__in' => array('subject', 'priority'),
                Q::not(array('field__flags__hasbit' => DynamicFormField::FLAG_CLIENT_VIEW)),
            )));
            // Skip display of forms without any answers
            foreach ($answers as $j=>$a) {
                if ($v = $a->display())
                    $sections[$i][$j] = array($v, $a);
            }
            // Set form titles
            $forms[$i] = $form->getTitle();
        }
        foreach ($sections as $i=>$answers) {
            ?>
                <table class="custom-data" cellspacing="0" cellpadding="4" width="100%" border="0">
                <tr><td colspan="2" class="headline flush-left"><?php echo $forms[$i]; ?></th></tr>
        <?php foreach ($answers as $A) {
            list($v, $a) = $A; ?>
                <tr>
                    <th><?php
        echo $a->getField()->get('label');
                    ?>:</th>
                    <td><?php
        echo $v;
                    ?></td>
                </tr>
        <?php } ?>
                </table>
            <?php
        } ?>
            </td>
        </tr>
        </table>
        <hr>
        <?php
            $email = $thisclient->getUserName();
            $clientId = TicketUser::lookupByEmail($email)->getId();
        ?>
        
        <h5 class="mb-3"><i class="bi bi-card-heading"></i> <?php echo __('Ticket thread');?></h5>

        <?php
            $ticket->getThread()->render(array('M', 'R', 'user_id' => $clientId), array(
                            'mode' => Thread::MODE_CLIENT,
                            'html-id' => 'ticketThread')
                        );
        ?>
        <?php
        if ($blockReply = $ticket->isChild() && $ticket->getMergeType() != 'visual')
            $warn = sprintf(__('This Ticket is Merged into another Ticket. Please go to the %s%d%s to reply.'),
                '<a href="tickets.php?id=', $ticket->getPid(), '" style="text-decoration:underline">Parent</a>');
        ?>

        <hr>

        <?php if($errors['err']) { ?>
            <div id="alert alert-danger mt-3"><?php echo $errors['err']; ?></div>
        <?php }elseif($msg) { ?>
            <div id="alert alert-info mt-3"><?php echo $msg; ?></div>
        <?php }elseif($warn) { ?>
            <div id="alert alert-warning mt-3"><?php echo $warn; ?></div>
        <?php }

        if ((!$ticket->isClosed() || $ticket->isReopenable()) && !$blockReply) { ?>
        <form class="mb-3" id="reply" action="tickets.php?id=<?php echo $ticket->getId();
        ?>#reply" name="reply" method="post" enctype="multipart/form-data">
            <?php csrf_token(); ?>
            <input type="hidden" name="id" value="<?php echo $ticket->getId(); ?>">
            <input type="hidden" name="a" value="reply">
            <h5 class="mt-4"><i class="bi bi-chat-dots"></i> <?php echo __('Post a Reply');?></h5>
            <div>
                <p><em><?php
                echo __('To best assist you, we request that you be specific and detailed'); ?></em>
                <font class="error">*&nbsp;<?php echo $errors['message']; ?></font>
                </p>
                <textarea 
                    name="<?php echo $messageField->getFormName(); ?>" 
                    id="message" cols="50" rows="9" wrap="soft"
                    class="<?php if ($cfg->isRichTextEnabled()) echo 'richtext'; ?> draft" 
                    <?php list($draft, $attrs) = Draft::getDraftAndDataAttrs(
                        'ticket.client', 
                        $ticket->getId(), 
                        $info['message']
                    ); echo $attrs; ?>>
                    <?php echo $draft ?: $info['message']; ?>
                </textarea>
            <?php
            if ($messageField->isAttachmentsEnabled()) {
                print $attachments->render(array('client'=>true));
            } ?>
            </div>
        <?php
        if ($ticket->isClosed() && $ticket->isReopenable()) { ?>
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-square"></i> <?php echo __('Ticket will be reopened on message post'); ?>
            </div>
        <?php } ?>
            <p class="mb-0 mt-3 text-center">
                <button class="btn btn-sm btn-primary shadow-sm mx-1" type="submit"><i class="bi bi-send"></i> <?php echo __('Post Reply');?></button>
                <button class="btn btn-sm btn-dark shadow-sm mx-1" type="reset"><i class="bi bi-arrow-counterclockwise"></i> <?php echo __('Reset');?></button>
                <button class="btn btn-sm btn-dark shadow-sm mx-1" type="button" onClick="history.go(-1)"><i class="bi bi-x-lg"></i> <?php echo __('Cancel');?></button>
            </p>
        </form>

        <?php
        } ?>
    </div>
</div>

<script type="text/javascript">
<?php
// Hover support for all inline images
$urls = array();
foreach (AttachmentFile::objects()->filter(array(
    'attachments__thread_entry__thread__id' => $ticket->getThreadId(),
    'attachments__inline' => true,
)) as $file) {
    $urls[strtolower($file->getKey())] = array(
        'download_url' => $file->getDownloadUrl(['type' => 'H']),
        'filename' => $file->name,
    );
} ?>
showImagesInline(<?php echo JsonDataEncoder::encode($urls); ?>);
</script>
