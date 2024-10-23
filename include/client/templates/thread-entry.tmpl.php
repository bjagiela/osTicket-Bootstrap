<?php
global $cfg;
$entryTypes = ThreadEntry::getTypes();
$user = $entry->getUser() ?: $entry->getStaff();

if ($entry->staff && $cfg->hideStaffName())
    $name = __('Staff');
else
    $name = $user ? $user->getName() : $entry->poster;
$avatar = '';

if ($cfg->isAvatarsEnabled() && $user)
    $avatar = $user->getAvatar();

$type = $entryTypes[$entry->type];
?>
    <div class="card-header <?php if ($type == "response") {echo "bg-warning-subtle bg-gradient";} else if ($type == "note") {echo "bg-info-subtle";}  ?>">
        <div class="row align-items-center">
            <!-- <img src="//www.gravatar.com/avatar/b1891d1dea3aeb76b4896e451623ac39?s=80&d=mm" alt="" width="40" class="rounded-circle shadow-sm" /> -->
            <div class="fw-bold col-auto me-auto">
                <?php if ($entry->flags & ThreadEntry::FLAG_EDITED) { ?>
                    <a 
                        tabindex="0" 
                        class="badge text-bg-dark" 
                        role="button" 
                        data-bs-toggle="popover" 
                        data-bs-trigger="focus"
                        data-bs-html="true"
                        data-bs-title="<i class='bi bi-pen'></i> <?php echo __("Entry was edited") ?>" 
                        data-bs-content="
                            <?php echo __('Date: ') ?>
                            <?php echo Format::datetime($entry->updated) ?>
                            <br/>
                            <?php echo __('Editor: ') ?>
                            <?php echo __('You') ?>">
                        <i class="bi bi-pen"></i>
                    </a>
                <?php } ?>
                <?php echo $name; ?>
            </div>
            <div class="small text-secondary col-auto">
                <?php echo sprintf('<time datetime="%s" title="%s">%s</time>',
                    date(DateTime::W3C, Misc::db2gmtime($entry->created)),
                    Format::daydatetime($entry->created),
                    Format::datetime($entry->created)
                ); ?>
            </div>
        </div>
    </div>

    <div class="card-body" id="thread-id-<?php echo $entry->getId(); ?>">
        <h5 class="card-title <?php if (!$entry->title) echo 'd-none'; ?>"><?php echo $entry->title; ?></h5>
        <div class="card-text thread-entry-generated-body">
            <?php echo $entry->getBody()->toHtml(); ?>
        </div>
    </div>

    <?php if ($entry->has_attachments) { ?>
        <div class="card-footer text-body-secondary d-flex flex-wrap flex-rg-1">
            <?php foreach ($entry->attachments as $A) {
                if ($A->inline)
                    continue;
                
                $size = '';
                if ($A->file->size)
                    $size = sprintf('<small class="filesize faded">%s</small>', Format::file_size($A->file->size));?>
                
                <a 
                    class="btn btn-sm btn-outline-dark me-2"
                    href="<?php echo $A->file->getDownloadUrl(['id' => $A->getId()]); ?>" 
                    download="<?php echo Format::htmlchars($A->getFilename()); ?>"
                    target="_blank">
                    <i class="bi bi-paperclip"></i> <?php echo Format::htmlchars($A->getFilename());?>
                    <span class="badge rounded-pill text-bg-dark ms-1"><?php echo $size;?></span>
                </a>
            <?php } ?>
        </div>
    <?php } ?>

    <?php
        if ($urls = $entry->getAttachmentUrls()) { ?>
            <script type="text/javascript">
                $('#thread-id-<?php echo $entry->getId(); ?>')
                    .data('urls', <?php echo JsonDataEncoder::encode($urls); ?>)
                    .data('id', <?php echo $entry->getId(); ?>);
            </script>
    <?php
    } ?>
