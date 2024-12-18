window.onload = function () {
    document.querySelectorAll('.truncate-cell').forEach(function (cell) {
        const lastname = cell.querySelector('.lastname');
        const firstname = cell.querySelector('.firstname');

        if (firstname) {
            const originalFirstname = firstname.textContent;

            if (cell.scrollWidth > cell.clientWidth) {
                let shortenedFirstname = originalFirstname;

                while (cell.scrollWidth > cell.clientWidth && shortenedFirstname.length > 0) {
                    shortenedFirstname = shortenedFirstname.slice(0, -1);
                    firstname.textContent = shortenedFirstname + '.';
                }

                if (shortenedFirstname.length === 0) {
                    firstname.style.display = 'none';
                }
            }
        }
    });
};
