const button = $('#yandex-test');

const testYandex = {
    OAuth: 'OAuth AgAAAAAw8-RIAAa_apLyY7o3wkFhhsEZn4ggt6o',
    yd_url: '',
    //Получить список директорий файлов
    getDirectory() {
        let getDatas = {
            'path': 'disk:/Приложения/hotgear_fileupload',
            'fields': '_embedded.items.name,_embedded.items.type',
            'limit': 100,
        }

        $.ajax({
            url: 'https://cloud-api.yandex.net/v1/disk/resources?path=' + getDatas.path + '&fields=' + getDatas.fields + '&limit=' + getDatas.limit,
            method: 'GET',
            //data: getDatas,
            contentType: 'application/json',
            headers: {'Authorization': testYandex.OAuth},
            success: function (data) {
                console.log(data);
                return data._embedded;
            },
            error: function (data) {
                return null;
            }
        })
    },

    //Создание папки
    createDirectory() {

    },

    //Получить ссылку на известный файл с Диска
    getDownloadUrl(yd_file = 'disk:/Приложения/hotgear_fileupload/Горы.jpg') {

        let path = '/uploads';
        $.ajax({
            url: 'https://cloud-api.yandex.net/v1/disk/resources/download?path=' + yd_file,
            method: 'GET',
            contentType: 'application/json',
            headers: {'Authorization': testYandex.OAuth},
            success: function (data) {
                testYandex.yd_url = data.href;
            },
            error: function (data) {
                return null;
            }
        })
    },

    downloadFile(yd_url) {
        $.ajax({
            url: yd_url,
            method: 'GET',
            headers: {'Authorization': testYandex.OAuth},
            success: function (data) {
                console.log(data)
            },
            error: function (data) {
                console.log(data)
            },
        })
    }

}

let href = testYandex.getDownloadUrl();


$.when(testYandex.getDownloadUrl()).done(function () {
    console.log(testYandex.yd_url);
})
$('#yandex-save').attr('href', href);

$('#yandex-save').on('click', function (e) {
    e.preventDefault();
    let yd_url = $(this).attr('href');
    testYandex.downloadFile(yd_url);
})