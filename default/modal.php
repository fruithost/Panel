<?php
    if(count($modals) > 0) {
        foreach($modals AS $modal) {
            ?>
                <div class="modal fade" id="<?php print $modal->getName(); ?>" tabindex="-1" role="dialog" aria-labelledby="<?php print $modal->getName(); ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <form method="post" class="ajax" action="<?php print $this->url('/ajax'); ?>">
                                <input type="hidden" name="modal" value="<?php print $modal->getName(); ?>" />
                                <div class="modal-header">
                                    <h5 class="modal-title"><?php print $modal->getTitle(); ?></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <?php print $modal->getContent($template); ?>
                                </div>
                                <div class="modal-footer text-center">
                                    <?php
                                        $buttons = $modal->getButtons();

                                        foreach($buttons AS $button) {
                                            if(is_array($button)) {
                                                foreach($button AS $entry) {
                                                    if($entry->isDismissable()) {
                                                        printf('<button type="button" name="%1$s" data-dismiss="modal" class="btn %3$s">%2$s</button>', $entry->getName(), $entry->getLabel(), $entry->getClasses(false));
                                                    } else {
                                                        printf('<button type="submit" name="%1$s" class="btn %3$s">%2$s</button>', $entry->getName(), $entry->getLabel(), $entry->getClasses(false));
                                                    }
                                                }
                                            } else {
                                                if($entry->isDismissable()) {
                                                    printf('<button type="button" name="%1$s" data-dismiss="modal" class="btn %3$s">%2$s</button>', $button->getName(), $button->getLabel(), $button->getClasses(false));
                                                } else {
                                                    printf('<button type="submit" name="%1$s" class="btn %3$s">%2$s</button>', $button->getName(), $button->getLabel(), $button->getClasses(false));
                                                }
                                            }
                                        }
                                    ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php
        }
    }
?>