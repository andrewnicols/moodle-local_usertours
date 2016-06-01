Description of Bootstrap Tour library import into Moodle

Instructions
------------
1. Clone https://github.com/sorich87/bootstrap-tour into an unrelated directory
2. Copy /build/js/bootstrap-tour.js to amd/src/bootstrap-tour.js
3. Add the following to the top of the file, before the bootstrap-tour.js code:

    // Start Moodle changes.
    /* jshint ignore:start */
    define(['jquery'], function(jQuery) {
    var factory =
    // End Moodle changes.

4. Add the following to the bottom of the file, after the bootstrap-tour.js code:

    // Start Moodle changes.
    return factory;/** @alias module:local_usertours/bootstrap-tour */
    });
    /* jshint ignore:end */
    // End Moodle changes.

5. Copy /build/css/bootstrap-tour.css to styles.css
6. Add the following to the bottom of the file:

    /* Start Moodle changes */

    .tour-step-background,
    .tour-backdrop {
      z-index: 4040;
    }
    .tour-step-backdrop,
    .tour-step-backdrop > td {
      z-index: 4041;
    }
    .popover[class*="tour-"] {
      z-index: 4042;
      min-width: 300px;
    }
    /* End Moodle changes */

7. Update thirdpartylibs.xml
