<?php
    $desc = $event->getDescription(ThreadEvent::MODE_CLIENT); 
    if (!$desc)
        return;
?>

<div class="thread-event <?php if ($event->uid) echo 'action'; ?> alert alert-light shadow-sm py-1 border border-secondary-subtle bg-body-secondary mx-0 my-3 text-dark">
    <div class="d-inline">
        <i class="icon-<?php echo $event->getIcon(); ?>"></i>
    </div>
    <span class="font-size-small"><?php echo $desc; ?></span>
</div>
