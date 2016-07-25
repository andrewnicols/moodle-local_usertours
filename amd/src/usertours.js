/**
 * Tour management code.
 *
 * @module     local_usertours/managetours
 * @class      managetours
 * @package    local_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 */
define(
['core/ajax', 'local_usertours/bootstrap-tour', 'jquery', 'core/templates', 'core/str'],
function(ajax, BootstrapTour, $, templates, str) {
    var usertours = {
        tourId: null,

        currentTour: null,

        context: null,

        init: function(tourId, startTour, context) {
            // Only one tour per page is allowed.
            usertours.tourId = tourId;

            usertours.context = context;

            if (typeof startTour === 'undefined') {
                startTour = true;
            }

            if (startTour) {
                // Fetch the tour configuration.
                usertours.fetchTour(tourId);
            }

            usertours.addResetLink();
            // Watch for the reset link.
            $('body').on('click', '[data-action="local_usertours/resetpagetour"]', function(e) {
                e.preventDefault();
                usertours.resetTourState(usertours.tourId);
            });
        },

        /**
         * Fetch the configuration specified tour, and start the tour when it has been fetched.
         *
         * @param   int     tourId      The ID of the tour to fetch.
         */
        fetchTour: function(tourId) {
            $.when(
                ajax.call([
                    {
                        methodname: 'local_usertours_fetch_tour',
                        args: {
                            tourid: tourId,
                            context: usertours.context
                        }
                    }
                ])[0],
                templates.render('local_usertours/tourstep', {})
            ).then(function(response, template) {
                usertours.startBootstrapTour(tourId, template[0], response.tourConfig);
            });
        },

        /**
         * Add a reset link to the page.
         */
        addResetLink: function() {
            str.get_string('resettouronpage', 'local_usertours')
                .done(function(s) {
                    // Grab the last item in the page of these.
                    $('footer, .logininfo')
                    .last()
                    .append(
                        '<div class="usertour">' +
                            '<a href="#" data-action="local_usertours/resetpagetour">' +
                                s +
                            '</a>' +
                        '</div>'
                    );
                });
        },

        /**
         * Start the specified tour.
         *
         * @param   int     tourId      The ID of the tour to start.
         * @param   object  config      The tour configuration.
         */
        startBootstrapTour: function(tourId, template, tourConfig) {
            if (usertours.currentTour) {
                tourConfig.onEnd = null;
                usertours.currentTour.end();
            }
            tourConfig.onEnd = usertours.markTourComplete;

            // Add the templtae to the configuration.
            // This enables translations of the buttons.
            tourConfig.template = template;

            usertours.currentTour = new BootstrapTour(tourConfig);

            usertours.currentTour.init(true);
            usertours.currentTour.start(true);
        },

        /**
         * Mark the specified tour as being completed by the user.
         *
         * @param   int     tourId      The ID of the tour to mark complete.
         */
        markTourComplete: function() {
            ajax.call([
                {
                    methodname: 'local_usertours_complete_tour',
                    args: {
                        tourid: usertours.tourId
                    }
                }
            ]);
        },

        /**
         * Reset the state, and restart the the tour on the current page.
         */
        resetTourState: function(tourId) {
            ajax.call([
                {
                    methodname: 'local_usertours_reset_tour',
                    args: {
                        path:   window.location.href,
                        tourid: tourId
                    },
                    done: function(response) {
                        if (response.startTour) {
                            usertours.fetchTour(response.startTour);
                        }
                    }
                }
            ]);
        }
    };

    return /** @alias module:local_usertours/usertours */ {
        /**
         * Initialise the user tour for the current page.
         *
         * @param   int     tourId      The ID of the tour to start.
         * @param   bool    startTour   Attempt to start the tour now.
         */
        init: usertours.init,

        /**
         * Reset the state, and restart the the tour on the current page.
         *
         * @param   int     tourId      The ID of the tour to restart.
         */
        resetTourState: usertours.resetTourState
    };
});
