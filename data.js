const exercises = {
    slabit: ["Alergare", "Bicicletă", "Jump rope"],
    masa: ["Bench Press", "Squat", "Deadlift"],
    mentenanta: ["Pushups", "Plank", "Rowing"]
};


/* modificat cu JQuery si am adaugat referinta in pagina profile*/
function updateExercises() {
    const goal = $("#goal").val();
    const exerciseSelect = $("#exercise");

    exerciseSelect.empty();

    $.each(exercises[goal],function(_, item) {
        exerciseSelect.append(
            $("<option>").val(item).text(item)
        );
    });
}

$(document).ready(function () {
    updateExercises();
})


function updateBirthDate() {
    const age = $("#age").val();
    const birthdate = $("#birthdate");

    const currentYear = new Date().getFullYear();
    birthdate.attr("min", `${currentYear - age-1}-12-31`);
    birthdate.attr("max", `${currentYear - age}-12-31`);
}

$(function() {
    $("#contactForm").on("submit", function (event) {
        event.preventDefault();

        let valid = true;
        $(".error").removeClass("error");

        const subject = $("#subject");
        if (subject.val().trim().length < 5) {
            subject.addClass("error");
            valid = false;
        }

        const message = $("#message");
        if (message.val().trim().length === 0) {
            message.addClass("error");
            valid = false;
        }

        if (valid) {
            alert("Mesaj trimis!");
        }
    });
});

function markInvalid($el) {
    $el.addClass("error");
}

function isPasswordValid(val) {
    return val.length >= 5 && /[A-Z]/.test(val) && /[0-9]/.test(val);
}

function isPhoneValid(val) {
    return /^\d{10}$/.test(val);
}


$(function(){
    $("#profileForm").on("submit", validateProfile)});


function validateProfile(event) {
    event.preventDefault();

    let valid = true;
    $(".error").removeClass("error");

    const password = $("#password");
    const age = $("#age");
    const birthdate = $("#birthdate");
    const phone = $("#phone");

    // password
    if (!isPasswordValid(password.val())) {
        markInvalid(password);
        valid = false;
    }

    // age
    const ageVal = Number(age.val());
    if (isNaN(ageVal) || ageVal < 14 || ageVal > 100) {
        markInvalid(age);
        valid = false;
    }

    // birthdate consistency
    if (birthdate.val()) {
        const birthYear = new Date(birthdate.val()).getFullYear();
        const currentYear = new Date().getFullYear();
        const expectedAge = currentYear - birthYear;

        if (Math.abs(expectedAge - ageVal) > 1) {
            markInvalid(birthdate);
            valid = false;
        }
    }

    // phone
    if (!isPhoneValid(phone.val())) {
        markInvalid(phone);
        valid = false;
    }

    if (valid) alert("Profil salvat!");
}