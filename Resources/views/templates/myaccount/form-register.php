<?php
/**
 * Mon Compte - Création
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @var App\Views\ViewController $this
 */

$this->skeletonLayoutBody();
$this->skeletonBlocks();
/*$this->setContentGridContainer('container-fluid');
$this->setContentGridRow('m-0');
$this->setContentGridCol('p-0');*/
?>

<section class="MyAccountRegister">
    <h3 class="Woocommerce-title">
        <?php _e('Création de compte', 'theme'); ?>
    </h3>

    <div class="row justify-content-between">

        <div class="col-12 col-lg-5 col-xl-4">
            <div class="MyAccountRegister-form MyAccountRegister-form--register">
                <?php echo form('register'); ?>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="MyAccountRegister-form MyAccountRegister-form--shipping">
                <?php echo form('shipping'); ?>
            </div>
        </div>

    </div>

</section>