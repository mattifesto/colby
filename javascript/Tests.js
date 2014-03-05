"use strict";


/**
 *
 */
var ColbyUnitTests = {};

/**
 * @return string
 */
ColbyUnitTests.runJavaScriptTests = function()
{
    var wasSuccessful = true;
    var now = new Date('2012/12/16 10:51 pm');
    var date = now;
    var tests = new Array();

    ColbyUnitTests.errors = '';

    // Setup tests

    tests.push({
        'date' : now,
        'string' : '0 seconds ago'
    });

    tests.push({
        'date' : new Date(now.getTime() - 1000),
        'string' : '1 seconds ago'
    });

    tests.push({
        'date' : new Date(now.getTime() - (1000 * 59)),
        'string' : '59 seconds ago'
    });

    tests.push({
        'date' : new Date(now.getTime() - (1000 * 60)),
        'string' : '1 minute ago'
    });

    tests.push({
        'date' : new Date(now.getTime() - (1000 * 60 * 2)),
        'string' : '2 minutes ago'
    });

    tests.push({
        'date' : new Date(now.getTime() - (1000 * 60 * 59)),
        'string' : '59 minutes ago'
    });

    tests.push({
        'date' : new Date('2012/12/16 9:51 pm'),
        'string' : 'Today at 9:51 p.m.'
    });

    tests.push({
        'date' : new Date('2012/12/16 12:00 am'),
        'string' : 'Today at 12:00 a.m.'
    });

    tests.push({
        'date' : new Date('2012/12/15 11:59 pm'),
        'string' : 'Yesterday at 11:59 p.m.'
    });

    tests.push({
        'date' : new Date('2012/12/15 12:00 am'),
        'string' : 'Yesterday at 12:00 a.m.'
    });

    tests.push({
        'date' : new Date('2012/12/14 1:53 pm'),
        'string' : 'December 14, 2012 1:53 p.m.'
    });

    // Run tests

    var countOfTests = tests.length;

    for (var i = 0; i < countOfTests; i++)
    {
        var string = Colby.dateToRelativeLocaleString(tests[i].date, now);

        if (string != tests[i].string)
        {
            ColbyUnitTests.errors += 'test failed\nexpected: "' + tests[i].string + '"\nreceived: "' + string + '"';

            wasSuccessful = false;

            break;
        }
    }

    /**
     * Test `Colby.centsToDollars`
     */

    tests = new Array();

    tests.push({
        "input" : 0,
        "expected" : "0.00"
    });

    tests.push({
        "input" : "020",
        "expected" : "0.20"
    });

    tests.push({
        "input" : "10",
        "expected" : "0.10"
    });

    tests.push({
        "input" : 110,
        "expected" : "1.10"
    });

    tests.push({
        "input" : 3234393,
        "expected" : "32343.93"
    });

    var countOfTests = tests.length;

    for (var i = 0; i < countOfTests; i++)
    {
        var output = Colby.centsToDollars(tests[i].input);

        if (output != tests[i].expected)
        {
            ColbyUnitTests.errors += "<div>`Colby.centsToDollars` test failed" +
                                     "<p>input: " + tests[i].input +
                                     "<p>output: " + output +
                                     "<p>expected: " + tests[i].expected +
                                     "</div>";

            wasSuccessful = false;

            break;
        }
    }

    /**
     * Report results
     */

    if (wasSuccessful)
    {
        return "Javascript unit tests passed.";
    }
    else
    {
        return "Javascript unit tests failed.\n\n" + ColbyUnitTests.errors;
    }
}
