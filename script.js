$(function () {
    $("#plannerForm").on("submit", validatePlanner);
})

function validatePlanner(event) {
    event.preventDefault();

    let valid = true;
    const name = $("#name");
    const age = $("#age");
    const weight = $("#weight");
    const height = $("#height");


    $(".error").removeClass("error");

    if (name.val().trim().length < 3) {
        name.addClass("error");
        valid = false;
    }

    if (isNaN(age.val()) || age.val() < 14 || age.val() > 100) {
        age.addClass("error");
        valid = false;
    }

    if (Number(weight.val()) <= 0) {
        weight.addClass("error");
        valid = false;
    }

    if (Number(height.val()) <= 0) {
        height.addClass("error");
        valid = false;
    }

    if (valid) {
        const heightM = height.val() / 100; // cm → m
        const bmi = (weight.val() / (heightM * heightM)).toFixed(2);
        alert(`Formularul trimis cu succes! \nBMI: ${bmi}`);
    }
}

/*Carusel*/
const slides = [
    {
        link: "index.html",
        text: "Home",
        image: "ImgCarusel/poza1.jpg",
    },
    {
        link: "planner.html",
        text: "Planner",
        image: "ImgCarusel/poza2.jpg",
    },
    {
        link: "contact.html",
        text: "Contact Us",
        image: "ImgCarusel/poza3.jpg",
    },
    {
        link: "profile.html",
        text: "Profil",
        image: "ImgCarusel/poza4.jpg",
    }
];

let currentSlide = 0;
let interval;

function showSlide(index) {
    $("#carusel").css("background-image", `url('${slides[index].image}')`);
    $("#caruselLink").attr("href", slides[index].link);
    $("#caruselText").text(slides[index].text);
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
    clearInterval(interval);
    startCarousel();
}

function prevSlide() {
    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
    showSlide(currentSlide);
    clearInterval(interval);
    startCarousel();
}

function startCarousel() {
    interval = setInterval(nextSlide, 3000);
}


$(document).ready(function () {
    showSlide(currentSlide);
    startCarousel();
})

/*Sortare tabele*/
function sortTable(columnIndex) {
    const $table = $("#exerciseTable");
    const $rows = $table.find("tr:gt(0)").toArray(); // toate randurile fara header

    $rows.sort((a, b) => {
        const valA = $(a).children().eq(columnIndex).text();
        const valB = $(b).children().eq(columnIndex).text();

        if (!isNaN(valA) && !isNaN(valB)) {
            return Number(valA) - Number(valB);
        }
        return valA.localeCompare(valB);
    });

    $.each($rows, function (_, row) {
        $table.append(row);
    });

    // indicare vizuala coloana sortata
    $table.find("tr:first th").each(function (i) {
        $(this).toggleClass("sorted", i === columnIndex);
    });
}

function sortVerticalTable(rowIndex) {
    const $table = $("#verticalTable");
    const $rows = $table.find("tr");

    const columns = [];

    for (let col = 1; col < $rows.first().children().length; col++) {
        let columnData = [];

        $rows.each(function () {
            columnData.push($(this).children().eq(col).text());
        });

        columns.push(columnData);
    }

    columns.sort((a, b) => {
        const valA = a[rowIndex];
        const valB = b[rowIndex];

        if (!isNaN(valA) && !isNaN(valB)) {
            return Number(valA) - Number(valB);
        }

        return valA.localeCompare(valB);
    });

    for (let col = 1; col < $rows.first().children().length; col++) {
        $rows.each(function (rowIndexInner) {
            $(this).children().eq(col).text(columns[col - 1][rowIndexInner]);
        });
    }

    // indicare vizuala rand sortat
    $rows.each(function (i) {
        $(this).children().eq(0).toggleClass("sorted", i === rowIndex);
    });
}

/* Liste colapsabile */
function toggleList(element) {
    const $el = $(element);
    const $sublist = $el.children("ul");

    $sublist.toggleClass("hidden");
    $el.toggleClass("open");
}

/*Dark Mode*/
$(document).ready(function () {
    const $lightBtn = $("#lightBtn");
    const $darkBtn = $("#darkBtn");

    function setActive(mode) {
        $lightBtn.removeClass("active");
        $darkBtn.removeClass("active");

        if (mode === "light") $lightBtn.addClass("active");
        if (mode === "dark") $darkBtn.addClass("active");
    }

    $lightBtn.on("click", function () {
        $("body").removeClass("dark");
        setActive("light");
    });

    $darkBtn.on("click", function () {
        $("body").addClass("dark");
        setActive("dark");
    });
});