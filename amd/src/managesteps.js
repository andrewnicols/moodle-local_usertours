/**
 * Step management code.
 *
 * @module     local_usertours/manage
 * @class      manage
 * @package    local_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 */
define(
['jquery', 'core/ajax', 'core/str', 'core/yui', 'core/templates', 'core/notification'],
function($, ajax, str, Y, templates, notification) {
    Y.use('moodle-core-notification-dialogue');
    var manager = {
        dialogue: null,

        getDialogue: function() {
            if (manager.dialogue === null) {
                manager.dialogue = new M.core.dialogue({
                    modal:      true,
                    visible:    false
                });
            }

            return manager.dialogue;
        },

        getDialogueNode: function() {
            var dialogue = manager.getDialogue();
            return $(dialogue.get('boundingBox').getDOMNode());
        },

        startAddStepDialogue: function() {
            ajax.call([
                {
                    methodname: 'local_usertours_get_targettypes',
                    args:       {},
                    done:       manager.displayTargetTypeList,
                    fail:       notification.exception
                }
            ]);
        },

        displayTargetTypeList: function(targetTypes) {
            templates.render('local_usertours/selecttarget', {targetTypes: targetTypes})
                .done(manager.updateDialogueContent)
                .fail(notification.exception)
                ;
        },

        handleSubmissionResponse: function(response) {
            if (response.template && response.context) {
                templates.render(response.template, response.context)
                    .done(manager.updateDialogueContent)
                    .fail(notification.exception)
                    ;
            }

            if (response.closeDialogue) {
                manager.getDialogue().hide();
            }

            if (response.redirectTo) {
                window.location.href = response.redirectTo;
            }
        },

        updateDialogueContent: function(content) {
            // Update the dialogue content.
            var dialogue = manager.getDialogue();

            dialogue
                .set('headerContent', str.get_string('selecttype', 'local_usertours'))
                .set('bodyContent', content)
                ;

            manager.getDialogueNode().find('form').submit(manager.handleSubmission);

            dialogue.show();
        },

        handleSubmission: function(e) {
            e.preventDefault();

            var target = $(e.target),
                formData = {};

            target.serializeArray().map(function(v) {
                formData[v.name] = v.value;
            });

            formData.tourid = manager.tourid;

            ajax.call([
                {
                    methodname: 'local_usertours_set_target',
                    args:       formData,
                    done:       manager.handleSubmissionResponse,
                    fail:       notification.exception
                }
            ]);
        },

        removeStep: function(e) {
            e.preventDefault();
            str.get_strings([
                {
                    key:        'confirmstepremovaltitle',
                    component:  'local_usertours'
                },
                {
                    key:        'confirmstepremovalquestion',
                    component:  'local_usertours'
                },
                {
                    key:        'yes',
                    component:  'moodle'
                },
                {
                    key:        'no',
                    component:  'moodle'
                }
            ]).done(function(s) {
                notification.confirm(s[0], s[1], s[2], s[3], $.proxy(function() {
                    window.location = $(this).attr('href');
                }, e.currentTarget));
            });
        },

        setup: function(tourid) {
            manager.tourid = tourid;
            $('.createstep').click(function(e) {
                e.preventDefault();
                manager.startAddStepDialogue();
            });

            $('body').delegate('[data-action="delete"]', 'click', manager.removeStep);
        }
    };

    return {
        setup: function(tourid) {
            manager.setup(tourid);
        }
    };
});
