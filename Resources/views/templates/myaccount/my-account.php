<?php
/**
 * Affichage du compte utilisateur.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var App\Views\ViewController $this
 */
$this->skeletonLayoutBody();
/*$this->setContentGridContainer('container-fluid');
$this->setContentGridRow('m-0');
$this->setContentGridCol('p-0');*/
?>

<section class="MyAccount">
    <div class="row">
        <div class="col-12 col-lg-3 col-xl-2">
            <?php
            $this->insert('wc.myaccount::navigation');
            ?>
        </div>

        <div class="col-12 col-lg-9 col-xl-10">
            <div class="MyAccountContent">
                <?php
                echo $this->section('content');
                ?>
            </div>
        </div>
    </div>
</section>

<?php $this->skeletonBlocks(); ?>