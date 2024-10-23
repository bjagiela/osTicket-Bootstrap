<?php
// Return if no visible fields
global $thisclient;
if (!$form->hasAnyVisibleFields($thisclient))
    return;

$isCreate = (isset($options['mode']) && $options['mode'] == 'create');
?>
    <tr>
        <td colspan="2">
            <h5 class="mb-0"><?php echo Format::htmlchars($form->getTitle()); ?></h5>
            <div class="text-dark-emphasis"><?php echo Format::display($form->getInstructions()); ?></div>
        </td>
    </tr>
    <?php
    // Form fields, each with corresponding errors follows. Fields marked
    // 'private' are not included in the output for clients
    foreach ($form->getFields() as $field) {
        try {
            if (!$field->isEnabled())
                continue;
        }
        catch (Exception $e) {
            // Not connected to a DynamicFormField
        }

        if ($isCreate) {
            if (!$field->isVisibleToUsers() && !$field->isRequiredForUsers())
                continue;
        } elseif (!$field->isVisibleToUsers()) {
            continue;
        }
        ?>
        <tr>
            <td colspan="2">
                <?php if (!$field->isBlockLevel()) { ?>
                    <label for="<?php echo $field->getFormName(); ?>"><span class="<?php if ($field->isRequiredForUsers()) echo 'required'; ?>">
                        <?php echo Format::htmlchars($field->getLocal('label')); ?>
                        <?php if ($field->isRequiredForUsers() &&
                                ($field->isEditableToUsers() || $isCreate)) { ?>
                            <span class="error">*</span>
                        <?php }
                        ?></span><?php
                            if ($field->get('hint')) { ?>
                                <br /><em><?php
                                    echo Format::viewableImages($field->getLocal('hint')); ?></em>
                            <?php
                            } ?>
                        <br/>
                <?php
                }
                if ($field->isEditableToUsers() || $isCreate) {?>
                    </label>
                    <?php 
                    $field->render(array('client'=>true));

                    foreach ($field->errors() as $e) { ?>
                        <div class="error"><?php echo $e; ?></div>
                    <?php }
                    
                    $field->renderExtras(array('client'=>true));
                } else {
                    $val = '';
                    if ($field->value)
                        $val = $field->display($field->value);
                    elseif (($a=$field->getAnswer()))
                        $val = $a->display();

                    echo sprintf('%s </label>', $val);
                }
                ?>
            </td>
        </tr>
        <?php
    }
?>
