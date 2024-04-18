<footer>
    <div class="footer-container">
        <div class="footer-menus">
            <?php
                wp_nav_menu(
                    array(
                        'fallback_cb' => function() { return ''; },
                        'container'  => 'nav',
                        'theme_location' => 'footer',
                    )
                );
            ?>
        </div>
        <div class="footer-copyright">
            <p>
                Copyright &copy;<?php echo date_i18n(_x( 'Y', 'copyright date format','default'));?> 
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo get_theme_mod('blog_title'); ?></a>     
                . All Rights Reserved.
            </p>
        </div>
    </div>
</footer>
<?php 
    wp_footer(); 
?>
