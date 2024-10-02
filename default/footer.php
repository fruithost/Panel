<?php
    /**
     * fruithost | OpenSource Hosting
     *
     * @author Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */

    use fruithost\Accounting\Auth;

        if(Auth::isLoggedIn()) {
            ?>
                            </main>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

		$template->foot();
	?>
	</body>
</html>