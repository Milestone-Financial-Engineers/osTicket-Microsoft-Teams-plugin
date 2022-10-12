<?php

require_once INCLUDE_DIR . 'class.plugin.php';

class teamsPluginConfig extends PluginConfig {

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
        return Plugin::translate('teams');
    }

    function pre_save(&$config, &$errors) {
        if ($config['teams-regex-subject-ignore'] && false === @preg_match("/{$config['teams-regex-subject-ignore']}/i", null)) {
            $errors['err'] = 'Your regex was invalid, try something like "spam", it will become: "/spam/i" when we use it.';
            return FALSE;
        }
        return TRUE;
    }

    function getOptions() {
        list ($__, $_N) = self::translate();

        return array(
            'teams'                      => new SectionBreakField(array(
                'label' => $__('teams notifier'),
                'hint'  => $__('Readme first: https://github.com/Milestone-Financial-Engineers/osTicket-Microsoft-Teams-plugin')
                    )),
            'teams-webhook-url'          => new TextboxField(array(
                'label'         => $__('Webhook URL'),
                'configuration' => array(
                    'size'   => 100,
                    'length' => 700
                ),
                    )),
            'teams-regex-subject-ignore' => new TextboxField([
                'label'         => $__('Ignore when subject equals regex'),
                'hint'          => $__('Auto delimited, always case-insensitive'),
                'configuration' => [
                    'size'   => 30,
                    'length' => 200
                ],
                    ]),
            'teams-update-types' => new ChoiceField([
                'label'         => $__('Update Types'),
                'hint'          => $__('What types of updates should be sent via teams?'),
                'choices' => array('both' => 'New & Updated Tickets', 'updatesOnly' => 'Only Ticket Updates', 'newOnly' => 'Only New Tickets'),
                'default' => 'both',
                'configuration' => [
                    'size'   => 30,
                    'length' => 200
                ],
                    ]),
            'message-template'           => new TextareaField([
                'label'         => $__('Message Template'),
                'hint'          => $__('The main text part of the teams message, uses Ticket Variables, for what the user typed, use variable: %{teams_safe_message}'),
                // "<%{url}/scp/tickets.php?id=%{ticket.id}|%{ticket.subject}>\n" // Already included as Title
                'default'       => "%{ticket.name.full} (%{ticket.email}) in *%{ticket.dept}* _%{ticket.topic}_\n\n```%{teams_safe_message}```",
                'configuration' => [
                    'html' => FALSE,
                ]
                    ])
        );
    }

}