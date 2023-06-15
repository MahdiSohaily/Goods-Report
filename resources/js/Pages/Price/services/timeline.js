export default function arrangeTime(dateTime) {
    const now = new Date();
    const create = new Date(dateTime);
    const diff = now - create;

    var msec = diff;
    var dd = Math.floor(msec / 1000 / 60 / 60 / 24);
    msec -= dd * 1000 * 60 * 60 * 24;
    var hh = Math.floor(msec / 1000 / 60 / 60);
    msec -= hh * 1000 * 60 * 60;

    var mm = Math.floor(msec / 1000 / 60);
    msec -= mm * 1000 * 60;

    var ss = Math.floor(msec / 1000);
    msec -= ss * 1000;

    let text = " ";

    if (dd) {
        text += ` ${dd}روز و `;
    }

    if (hh) {
        text += `${hh} ساعت `;
    }

    if (!hh && mm) {
        text += `${mm} دقیقه `;
    }

    if (!mm && !hh) {
        text += `${ss} ثانیه `;
    }

    return `${text} قبل`;
}
