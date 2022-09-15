window.onload = function () {
    let iframe = document.createElement('iframe');
    iframe.setAttribute('loading', 'lazy');
    iframe.setAttribute('width', tableon_remote_width);
    iframe.setAttribute('height', tableon_remote_height);
    iframe.setAttribute('frameborder', 0);
    iframe.setAttribute('id', 'iframe-' + tableon_remote_anchor);

    let style = 'overflow-y:hidden;';
    if (!tableon_remote_height) {
        style += 'height: 115vh;';
        iframe.setAttribute('scrolling', 'no');
    }
    iframe.setAttribute('style', style);


    //*** lets make listening of get params, for example for currency switcher or posts filter
    let link_tail = '?';
    let params = new URLSearchParams(parent.window.location.search);

    entries = params.entries();
    for (const entry of entries) {
        link_tail += entry[0] + '=' + entry[1];
    }

    tableon_remote_src += link_tail;

    //+++

    if (tableon_link_get_data) {
        try {
            const keys = Object.keys(tableon_link_get_data);
            if (keys.length) {
                for (const key of keys) {
                    if (key.substring(0, 5) === 'tableon_') {
                        tableon_remote_src += '&' + key + '=' + tableon_link_get_data[key];
                    }
                }
            }
        } catch (e) {
            console.log(e);
        }
    }
    
    iframe.setAttribute('src', tableon_remote_src);
    document.querySelector(tableon_remote_anchor).appendChild(iframe);
};

