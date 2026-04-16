"use strict";

(function () {
    function hasFlatpickr() {
        return typeof window.flatpickr === "function";
    }

    function syncAltInputAttributes(fp, input) {
        if (!fp || !fp.altInput || !input) return;

        fp.altInput.required = !!input.required;
        fp.altInput.disabled = !!input.disabled;
        fp.altInput.readOnly = false;
        fp.altInput.autocomplete = "off";

        var describedBy = input.getAttribute("aria-describedby");
        if (describedBy) fp.altInput.setAttribute("aria-describedby", describedBy);

        var placeholder = input.getAttribute("placeholder");
        if (placeholder) fp.altInput.setAttribute("placeholder", placeholder);
    }

    function initDatePicker(input) {
        if (!input || input._flatpickr || !hasFlatpickr()) return;

        var cssClass = (input.getAttribute("class") || "").trim();
        var altClass = cssClass || "form-control";

        window.flatpickr(input, {
            allowInput: true,
            altInput: true,
            altInputClass: altClass,
            altFormat: "M j, Y",
            dateFormat: "Y-m-d",
            minDate: input.getAttribute("min") || undefined,
            maxDate: input.getAttribute("max") || undefined,
            disableMobile: true,
            onReady: function (_, __, fp) {
                syncAltInputAttributes(fp, input);
            }
        });
    }

    function initDateTimePicker(input) {
        if (!input || input._flatpickr || !hasFlatpickr()) return;

        var cssClass = (input.getAttribute("class") || "").trim();
        var altClass = cssClass || "form-control";

        window.flatpickr(input, {
            allowInput: true,
            altInput: true,
            altInputClass: altClass,
            altFormat: "M j, Y g:i K",
            dateFormat: "Y-m-d\\TH:i",
            enableTime: true,
            time_24hr: false,
            minDate: input.getAttribute("min") || undefined,
            maxDate: input.getAttribute("max") || undefined,
            disableMobile: true,
            onReady: function (_, __, fp) {
                syncAltInputAttributes(fp, input);
            }
        });
    }

    function initTimePicker(input) {
        if (!input || input._flatpickr || !hasFlatpickr()) return;

        var cssClass = (input.getAttribute("class") || "").trim();
        var altClass = cssClass || "form-control";

        window.flatpickr(input, {
            allowInput: true,
            altInput: true,
            altInputClass: altClass,
            altFormat: "h:i K",
            dateFormat: "H:i",
            enableTime: true,
            noCalendar: true,
            time_24hr: false,
            disableMobile: true,
            onReady: function (_, __, fp) {
                syncAltInputAttributes(fp, input);
            }
        });
    }

    function initAll(scope) {
        if (!scope) scope = document;
        if (!hasFlatpickr()) return;

        Array.prototype.forEach.call(scope.querySelectorAll('input[type="date"]'), initDatePicker);
        Array.prototype.forEach.call(scope.querySelectorAll('input[type="datetime-local"]'), initDateTimePicker);
        Array.prototype.forEach.call(scope.querySelectorAll('input[type="time"]'), initTimePicker);
    }

    function setPickerValue(input, value) {
        if (!input) return;

        if (input._flatpickr) {
            if (!value) {
                input._flatpickr.clear();
                return;
            }
            input._flatpickr.setDate(value, true);
            return;
        }

        input.value = value || "";
    }

    window.AdminDatePickers = {
        init: initAll,
        setValue: setPickerValue
    };

    document.addEventListener("DOMContentLoaded", function () {
        initAll(document);
    });
})();
