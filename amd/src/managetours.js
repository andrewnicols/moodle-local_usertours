/**
 * Tour management code.
 *
 * @module     local_usertours/managetours
 * @class      managetours
 * @package    local_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 */
define(
['jquery', 'core/ajax', 'core/str', 'core/notification'],
function($, ajax, str, notification) {
    var manager = {
        removeTour: function(e) {
            e.preventDefault();

            str.get_strings([
                {
                    key:        'confirmtourremovaltitle',
                    component:  'local_usertours'
                },
                {
                    key:        'confirmtourremovalquestion',
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

        setup: function() {
            $('body').delegate('[data-action="delete"]', 'click', manager.removeTour);
        }
    };

    return {
        setup: manager.setup
    };
});
