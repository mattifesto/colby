<?php

final class CBDocumentation {

    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'help'
        ];
    }



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Documentation';

        CBView::renderSpec((object)[
            'className' => 'CBPageTitleAndDescriptionView',
        ]);

        $menuModel = CBModelCache::fetchModelByID(CBHelpAdminMenu::ID());

        ?><div class="CBDocumentation_container"><?php

        $items = CBModel::valueToArray($menuModel, 'items');

        usort(
            $items,
            function ($a, $b) {
                $atext = CBModel::valueToString($a, 'text');
                $btext = CBModel::valueToString($b, 'text');

                return $atext <=> $btext;
            }
        );

        array_walk(
            $items,
            function ($item) {
                $textAsHTML = cbhtml(CBModel::valueToString($item, 'text'));
                $URLAsHTML = cbhtml(CBModel::valueToString($item, 'URL'));

                ?>

                <div>
                    <a href="<?= $URLAsHTML ?>"><?= $textAsHTML ?></a>
                </div>

                <?php
            }
        );

        ?></div><?php

        CBView::render(
            (object)[
                'className' => 'CBDocumentation_ClassListView',
            ]
        );
    }
    /* CBAdmin_render() */



    /* -- CBAjax interfaces -- */



    /**
     * @param object $args
     *
     *      {
     *          targetClassName: string
     *      }
     *
     * @return void
     */
    static function CBAjax_createDocumentationFile(
        stdClass $args
    ): void {
        $targetClassName = CBModel::valueToString(
            $args,
            'targetClassName'
        );

        $targetClassFilepath = Colby::findFile(
            "classes/{$targetClassName}/{$targetClassName}.php"
        );

        if ($targetClassFilepath === null) {
            throw new CBExceptionWithValue(
                'This class was not found.',
                $targetClassFilepath,
                '6d98c1a8a9196c22124daa5eb8857279f28a426d'
            );
        }

        $targetClassDocumentationFilepath = (
            dirname(
                $targetClassFilepath
            ) .
            '/' .
            $targetClassName .
            '_CBDocumentation_description.cbmessage'
        );

        if (!file_exists($targetClassDocumentationFilepath)) {
            touch($targetClassDocumentationFilepath);
        }
    }
    /* CBAjax_createDocumentationFile() */



    /**
     * @return string
     */
    static function CBAjax_createDocumentationFile_getUserGroupClassName(
    ): string {
        return 'CBDevelopersUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v635.css', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v633.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBAjax',
            'CBUI',
            'CBUIPanel',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
/* CBDocumentation */



/**
 *
 */
final class CBDocumentation_ClassListView {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBContentStyleSheet',
        ];
    }



    /* -- CBView interfaces -- */



    /**
     * @param object $viewModel
     *
     * @return void
     */
    static function CBView_render(
        stdClass $viewModel
    ): void {
        $classNames = CBLibrary::getAllClassDirectoryNames();

        ?>

        <div class="CBDocumentation_container">
            <div class="CBUI_title1">
                Classes
            </div>

            <?php

            sort($classNames);

            foreach ($classNames as $className) {
                $classDocumentationURL = (
                    '/admin/' .
                    '?c=CBAdmin_CBDocumentationForClass' .
                    '&className=' .
                    urlencode(
                        $className
                    )
                );

                ?>

                <div>
                    <a href="<?= $classDocumentationURL ?>">
                        <?= cbhtml($className) ?>
                    </a>
                </div>

                <?php

                CBView::render(
                    (object)[
                        'className' => 'CBDocumentation_DeveloperView',
                        'targetClassName' => $className,
                    ]
                );
            }

            ?>

        </div>

        <?php
    }
    /* CBView_render() */

}
/* CBDocumentation_ClassListView */



/**
 *
 */
final class CBDocumentation_DeveloperView {

    /* -- CBView interfaces -- */



    /**
     * @param object $viewModel
     *
     * @return void
     */
    static function CBView_render(
        stdClass $viewModel
    ): void {
        $userIsDeveloper = (
            CBUserGroup::currentUserIsMemberOfUserGroup(
                'CBDevelopersUserGroup'
            )
        );

        if ($userIsDeveloper !== true) {
            return;
        }

        $targetClassName = CBModel::valueToString(
            $viewModel,
            'targetClassName'
        );

        $targetClassFilepath = Colby::findFile(
            "classes/{$targetClassName}/{$targetClassName}.php"
        );

        if ($targetClassFilepath === null) {
            $classDirectories = CBLibrary::getClassDirectories(
                $targetClassName
            );

            ?>

            <dd class="CBDocumentation_DeveloperView_error">
                Warning. There is no class file in:

                <ul>
                    <?php

                        foreach ($classDirectories as $classDirectory) {
                            echo (
                                '<li><code>' .
                                cbhtml($classDirectory) .
                                '</code></li>'
                            );
                        }

                    ?>
                </ul>
            </dd>

            <?php

            return;
        }

        $documentationFilepath = (
            dirname(
                $targetClassFilepath
            ) .
            '/' .
            $targetClassName .
            '_CBDocumentation_description.cbmessage'
        );

        if (file_exists($documentationFilepath)) {
            return;
        }

        ?>

        <dd
            class="CBDocumentation_DeveloperView"
            data-target-class-name="<?= cbhtml($targetClassName) ?>"
        >
        </dd>

        <?php
    }
    /* CBView_render() */

}
/* CBDocumentation_DeveloperView */
