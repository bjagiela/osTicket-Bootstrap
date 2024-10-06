<div class="">
    <div class="container pt-4">
        <div class="row align-items-center">
            <div class="col-12 col-xl-6">
                <div class="p-3">
                    <h1 class="fw-semibold display-5"><?php echo __('How can we help today?');?></h1>
                    <p class="fs-5 lead" ><?php echo Format::htmlchars((string) $ost->company ?: 'osTicket.com'); ?> <?php echo __('support system provides our customers with easy-to-use and trouble-free service experience. To start using the service, log in using the button below.');?></p>
                    <p class="mt-5">
                        <a class="btn btn-lg btn-dark bg-gradient shadow" href="#" role="button"><i class="bi bi-door-open"></i> <?php echo __('Log In');?></a>
                        <a class="text-dark d-block mt-2" href=""><?php echo __("I don't have an account");?></a>
                    </p>
                </div>
            </div>
            <div class="col-6 d-none d-xl-block">
                <img class="p-3" src="<?php echo ROOT_PATH ?>images/customer-service.svg" alt="">
            </div>
        </div>
    </div>

    <div class="container mt-5 pb-5">
        <div class="row g-4">
            <div class="col">
                <div class="px-4 py-3 h-100 glasscard">
                    <div class="text-center">
                        <img style="height: 250px;" src="<?php echo ROOT_PATH ?>images/timeline-50.svg" alt="">
                    </div>  
                    <h3 class="text-secondary-emphasis text-center"><?php echo __('Always on Time');?></h3>
                    <p class="text-dark-emphasis">Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
                </div>
            </div>
            <div class="col">
                <div class="px-4 py-3 h-100 glasscard">
                    <div class="text-center">
                        <img style="height: 250px;" src="<?php echo ROOT_PATH ?>images/problem-solving-5-2.svg" alt="">
                    </div>
                    <h3 class="text-secondary-emphasis text-center"><?php echo __('Quick and Solid Support');?></h3>
                    <p class="text-dark-emphasis">Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
                </div>
            </div>
            <div class=" col">
                <div class="px-4 py-3 h-100 glasscard">
                    <div class="text-center">
                        <img style="height: 250px;" src="<?php echo ROOT_PATH ?>images/cybersecurity-1-1.svg" alt="">
                    </div>
                    <h3 class="text-secondary-emphasis text-center"><?php echo __('People You Can Trust');?></h3>
                    <p class="text-dark-emphasis">Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
                </div>
            </div>
        </div>
    </div>
</div>