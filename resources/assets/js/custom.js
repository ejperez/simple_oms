/**
 * Created by EJ Perez on 6/9/2015.
 * JavaScript helper functions
 */

/**
 * Number.prototype.format(n, x, s, c)
 *
 * @param integer n: length of decimal
 * @param integer x: length of whole part
 * @param mixed   s: sections delimiter
 * @param mixed   c: decimal delimiter
 */
Number.prototype.format = function(n, x, s, c) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = this.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};

// To money format
Number.prototype.toMoney = function(){
    return this.format(2, 3, ',', '.');
}

// Add days to JS date
Date.prototype.addDays = function(days)
{
    var dat = new Date(this.valueOf());
    dat.setDate(dat.getDate() + days);
    return dat;
}

// Datepicker fix
jQuery.browser = {};
(function () {
    jQuery.browser.msie = false;
    jQuery.browser.version = 0;
    if (navigator.userAgent.match(/MSIE ([0-9]+)\./)) {
        jQuery.browser.msie = true;
        jQuery.browser.version = RegExp.$1;
    }
})();

// Conditional Handlebars helper
Handlebars.registerHelper('ifCond', function (v1, operator, v2, options) {
    switch (operator) {
        case '==':
            return (v1 == v2) ? options.fn(this) : options.inverse(this);
        case '!=':
            return (v1 != v2) ? options.fn(this) : options.inverse(this);
        case '===':
            return (v1 === v2) ? options.fn(this) : options.inverse(this);
        case '<':
            return (v1 < v2) ? options.fn(this) : options.inverse(this);
        case '<=':
            return (v1 <= v2) ? options.fn(this) : options.inverse(this);
        case '>':
            return (v1 > v2) ? options.fn(this) : options.inverse(this);
        case '>=':
            return (v1 >= v2) ? options.fn(this) : options.inverse(this);
        case '&&':
            return (v1 && v2) ? options.fn(this) : options.inverse(this);
        case '||':
            return (v1 || v2) ? options.fn(this) : options.inverse(this);
        default:
            return options.inverse(this);
    }
});

// Checking if class name exists
Handlebars.registerHelper('hasClass', function (subject, testClass, options) {
    return (subject.indexOf(testClass) != -1) ? options.fn(this) : options.inverse(this);
});

// For striping table rows
Handlebars.registerHelper('oddEven', function(index) {
    return index % 2 == 0 ? 'even' : 'odd';
});

// Simulates PHP's date function
Date.prototype.format = function (e) {
    var t = "";
    var n = Date.replaceChars;
    for (var r = 0; r < e.length; r++) {
        var i = e.charAt(r);
        if (r - 1 >= 0 && e.charAt(r - 1) == "\\") {
            t += i
        } else if (n[i]) {
            t += n[i].call(this)
        } else if (i != "\\") {
            t += i
        }
    }
    return t
};
Date.replaceChars = {
    shortMonths : ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
    longMonths : ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
    shortDays : ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
    longDays : ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
    d : function () {
        return (this.getDate() < 10 ? "0" : "") + this.getDate()
    },
    D : function () {
        return Date.replaceChars.shortDays[this.getDay()]
    },
    j : function () {
        return this.getDate()
    },
    l : function () {
        return Date.replaceChars.longDays[this.getDay()]
    },
    N : function () {
        return this.getDay() + 1
    },
    S : function () {
        return this.getDate() % 10 == 1 && this.getDate() != 11 ? "st" : this.getDate() % 10 == 2 && this.getDate() != 12 ? "nd" : this.getDate() % 10 == 3 && this.getDate() != 13 ? "rd" : "th"
    },
    w : function () {
        return this.getDay()
    },
    z : function () {
        var e = new Date(this.getFullYear(), 0, 1);
        return Math.ceil((this - e) / 864e5)
    },
    W : function () {
        var e = new Date(this.getFullYear(), 0, 1);
        return Math.ceil(((this - e) / 864e5 + e.getDay() + 1) / 7)
    },
    F : function () {
        return Date.replaceChars.longMonths[this.getMonth()]
    },
    m : function () {
        return (this.getMonth() < 9 ? "0" : "") + (this.getMonth() + 1)
    },
    M : function () {
        return Date.replaceChars.shortMonths[this.getMonth()]
    },
    n : function () {
        return this.getMonth() + 1
    },
    t : function () {
        var e = new Date;
        return (new Date(e.getFullYear(), e.getMonth(), 0)).getDate()
    },
    L : function () {
        var e = this.getFullYear();
        return e % 400 == 0 || e % 100 != 0 && e % 4 == 0
    },
    o : function () {
        var e = new Date(this.valueOf());
        e.setDate(e.getDate() - (this.getDay() + 6) % 7 + 3);
        return e.getFullYear()
    },
    Y : function () {
        return this.getFullYear()
    },
    y : function () {
        return ("" + this.getFullYear()).substr(2)
    },
    a : function () {
        return this.getHours() < 12 ? "am" : "pm"
    },
    A : function () {
        return this.getHours() < 12 ? "AM" : "PM"
    },
    B : function () {
        return Math.floor(((this.getUTCHours() + 1) % 24 + this.getUTCMinutes() / 60 + this.getUTCSeconds() / 3600) * 1e3 / 24)
    },
    g : function () {
        return this.getHours() % 12 || 12
    },
    G : function () {
        return this.getHours()
    },
    h : function () {
        return ((this.getHours() % 12 || 12) < 10 ? "0" : "") + (this.getHours() % 12 || 12)
    },
    H : function () {
        return (this.getHours() < 10 ? "0" : "") + this.getHours()
    },
    i : function () {
        return (this.getMinutes() < 10 ? "0" : "") + this.getMinutes()
    },
    s : function () {
        return (this.getSeconds() < 10 ? "0" : "") + this.getSeconds()
    },
    u : function () {
        var e = this.getMilliseconds();
        return (e < 10 ? "00" : e < 100 ? "0" : "") + e
    },
    e : function () {
        return "Not Yet Supported"
    },
    I : function () {
        var e = null;
        for (var t = 0; t < 12; ++t) {
            var n = new Date(this.getFullYear(), t, 1);
            var r = n.getTimezoneOffset();
            if (e === null)
                e = r;
            else if (r < e) {
                e = r;
                break
            } else if (r > e)
                break
        }
        return this.getTimezoneOffset() == e | 0
    },
    O : function () {
        return (-this.getTimezoneOffset() < 0 ? "-" : "+") + (Math.abs(this.getTimezoneOffset() / 60) < 10 ? "0" : "") + Math.abs(this.getTimezoneOffset() / 60) + "00"
    },
    P : function () {
        return (-this.getTimezoneOffset() < 0 ? "-" : "+") + (Math.abs(this.getTimezoneOffset() / 60) < 10 ? "0" : "") + Math.abs(this.getTimezoneOffset() / 60) + ":00"
    },
    T : function () {
        var e = this.getMonth();
        this.setMonth(0);
        var t = this.toTimeString().replace(/^.+ \(?([^\)]+)\)?$/, "$1");
        this.setMonth(e);
        return t
    },
    Z : function () {
        return -this.getTimezoneOffset() * 60
    },
    c : function () {
        return this.format("Y-m-d\\TH:i:sP")
    },
    r : function () {
        return this.toString()
    },
    U : function () {
        return this.getTime() / 1e3
    }
}
