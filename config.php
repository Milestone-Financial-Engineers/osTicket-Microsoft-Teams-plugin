<?php

require_once INCLUDE_DIR . 'class.plugin.php';

class TeamsPluginConfig extends PluginConfig {

    // Provide compatibility function for versions of osTicket prior to
    // translation support (v1.9.4)
    function translate() {
        if (!method_exists('Plugin', 'translate')) {
            return array(
                function ($x) {
                    return $x;
                },
                function ($x, $y, $n) {
                    return $n != 1 ? $y : $x;
                }
            );
        }
        return Plugin::translate('Teams');
    }

    function pre_save(&$config, &$errors) {
        if ($config['Teams-regex-subject-ignore'] && false === @preg_match("/{$config['Teams-regex-subject-ignore']}/i", null)) {
            $errors['err'] = 'Your regex was invalid, try something like "spam", it will become: "/spam/i" when we use it.';
            return FALSE;
        }
        return TRUE;
    }

    function getOptions() {
        list ($__, $_N) = self::translate();

        return array(
            'Teams'                      => new SectionBreakField(array(
                'label' => $__('Teams notifier'),
                'hint'  => $__('Readme first: https://github.com/Milestone-Financial-Engineers/osTicket-Microsoft-Teams-plugin')
                    )),
            'Teams-webhook-url'          => new TextboxField(array(
                'label'         => $__('Webhook URL'),
                'configuration' => array(
                    'size'   => 100,
                    'length' => 700
                ),
                    )),
            'Teams-regex-subject-ignore' => new TextboxField([
                'label'         => $__('Ignore when subject equals regex'),
                'hint'          => $__('Auto delimited, always case-insensitive'),
                'configuration' => [
                    'size'   => 30,
                    'length' => 200
                ],
                    ]),
            'Teams-message-display' => new BooleanField([
                'label' => $__('Display ticket message body in notification.'),
                'hint' => $__('Uncheck to hide messages.'),
                'default' => TRUE
            ])
);
    }

}