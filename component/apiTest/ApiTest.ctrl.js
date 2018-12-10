(function () {
    document.querySelector('body')
        .addEventListener('apiResponse', function (data) {
        console.log(data.detail)
    });
})();