<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark text-uppercase">
    <div class="container">
        <a class="navbar-brand" href="<?php echo HTML::siteUrl(); ?>">
            <span class="text-white"><?php echo $site->title(); ?></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                <!-- Static pages -->
                <?php foreach ($staticContent as $staticPage): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $staticPage->permalink() ?>"><?php echo $staticPage->title() ?></a>
                </li>
                <?php endforeach ?>

                <!-- Social Networks -->
                <?php foreach (HTML::socialNetworks() as $key=>$label): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $site->{$key}(); ?>" target="_blank">
                        <img class="d-none d-sm-block nav-svg-icon" src="<?php echo DOMAIN_THEME.'img/'.$key.'.svg' ?>" alt="<?php echo $label ?>" />
                        <span class="d-inline d-sm-none"><?php echo $label; ?></span>
                    </a>
                </li>
                <?php endforeach; ?>

            </ul>
        </div>
    </div>
</nav>