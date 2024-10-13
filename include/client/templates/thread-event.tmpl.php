<?php
    $desc = $event->getDescription(ThreadEvent::MODE_CLIENT); 
    if (!$desc)
        return;
?>

<div class="thread-event <?php if ($event->uid) echo 'action'; ?>">
    <i class="icon-<?php echo $event->getIcon(); ?>"></i>
    <span class=""><?php echo $desc; ?></span>
</div>
