/**
 * Just two examples about DataTable usage
 * https://datatables.net/manual/server-side
 * 
 */


$(document).ready(function() {

    $('#example_client_side_processing').DataTable( {
        ajax: {
            'url': 'mock/client_side_processing.json',
            'dataSrc': ''
        },
        columns: [ // see https://datatables.net/reference/option/columns for options
            {
                'data': 'firstname',
                'title': 'First Name'
            },
            {
                'data': 'lastname',
                'title': 'Last Name'
            },
            {
                'data': 'fullname',
                'title': 'Full Name'
            },
            {
                'data': 'email',
                'title': 'Email'
            },
            {
                'data': 'phone_number',
                'title': 'Phone Number'
            },
            {
                'data': 'avatar',
                'title': 'Avatar'
            },
            {
                'data': 'photo',
                'title': 'Photo', 
                'render': function ( data, type, full, meta ) { // see https://datatables.net/reference/option/columns.data
                    if (type !== 'display') {
                        return data;
                    }

                    if (!data || !data.webPath) {
                        return '';
                    }

                    return '<img src="' + data.webPath +'" style="width:50px"></img>';
                }
            },
            {
                'data': 'available',
                'title': 'Available',
                'render': function ( data, type, full, meta ) {
                    if (type !== 'display') {
                        return data;
                    }

                    return data ? 'oui': 'non';
                }
            }
        ]
    } );

    $('#example_server_side_processing').DataTable( {
        ajax: {
            'url': 'mock/server_side_processing.json',
            'dataSrc': 'data'
        },
        serverSide: true, // see https://datatables.net/manual/server-side
        columns: [ // see https://datatables.net/reference/option/columns for options
            {
                'data': 'firstname',
                'title': 'First Name'
            },
            {
                'data': 'lastname',
                'title': 'Last Name'
            },
            {
                'data': 'fullname',
                'title': 'Full Name'
            },
            {
                'data': 'email',
                'title': 'Email'
            },
            {
                'data': 'phone_number',
                'title': 'Phone Number'
            },
            {
                'data': 'avatar',
                'title': 'Avatar'
            },
            {
                'data': 'photo',
                'title': 'Photo', 
                'render': function ( data, type, full, meta ) { // see https://datatables.net/reference/option/columns.data
                    if (type !== 'display') {
                        return data;
                    }

                    if (!data || !data.webPath) {
                        return '';
                    }

                    return '<img src="' + data.webPath +'" style="width:50px"></img>';
                }
            },
            {
                'data': 'available',
                'title': 'Available',
                'render': function ( data, type, full, meta ) {
                    if (type !== 'display') {
                        return data;
                    }

                    return data ? 'oui': 'non';
                }
            }
        ]
    } );

} );