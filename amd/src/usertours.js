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
                            tourid:     tourId,
                            context:    usertours.context,
                            pageurl:    window.location.href,
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
                // End the current tour, but disable end tour handler.
                tourConfig.onEnd = null;
                usertours.currentTour.end();
            }

            // Add the various handlers.
            tourConfig.onEnd = usertours.markTourComplete;
            tourConfig.onShown = usertours.markStepShown;

            // Add the template to the configuration.
            // This enables translations of the buttons.
            tourConfig.template = template;

            usertours.currentTour = new BootstrapTour(tourConfig);

            usertours.currentTour.init(true);
            usertours.currentTour.start(true);
        },

        /**
         * Mark the specified step as being shownd by the user.
         *
         * @param   Tour    tour     The data from the step.
         */
        markStepShown: function(tour) {
            ajax.call([
                {
                    methodname: 'local_usertours_step_shown',
                    args: {
                        tourid:     usertours.tourId,
                        context:    usertours.context,
                        pageurl:    window.location.href,
                        stepid:     tour.getStep(tour.getCurrentStep()).stepid,
                        stepindex:  tour.getCurrentStep(),
                    }
                }
            ]);
        },

        /**
         * Mark the specified tour as being completed by the user.
         *
         * @param   Tour    our     The data from the tour.
         */
        markTourComplete: function(tour) {
            ajax.call([
                {
                    methodname: 'local_usertours_complete_tour',
                    args: {
                        tourid:     usertours.tourId,
                        context:    usertours.context,
                        pageurl:    window.location.href,
                        stepid:     tour.getStep(tour.getCurrentStep()).stepid,
                        stepindex:  tour.getCurrentStep(),
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
                        tourid:     tourId,
                        context:    usertours.context,
                        pageurl:    window.location.href,
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
