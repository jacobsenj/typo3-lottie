# [EXT:lottie][ter lottie] – TYPO3 CMS extension to render Lottie/Bodymovin animations.

This extension will add a new field `sys_file_metadata.tx_lottie_is_lottie_animation`.
This new field will only be displayed for JSON files.
Additionally `$GLOBALS['TYPO3_CONF_VARS']['SYS']['mediafile_ext']` will be altered, to allow JSON
files to be added to `tt_content.assets`, which is only present in `textmedia` CType by default.

*INFO:* `Resources/Public/JavaScript/dist` contains generated files that can be re-build with Gulp.
Please have a look into `Resources/Private/Build/gulpfile.js`.
Those generated files are added to the repository so this extension can be run out of the box.
Please include the static TypoScript Template to use those files or you can include the necessary
JavaScript yourself.


[ter lottie]: https://extensions.typo3.org/extension/lottie/
