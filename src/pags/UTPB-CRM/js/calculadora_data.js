var datos = {
    "comision": 700,
    "empresas": [
        {
            "id": 1,
            "nombre": "Universidad Santander",
            "slug": "unisant",
            "productos": [
                {
                    "id": 1,
                    "nombre": "Licenciatura en contaduría",
                    "tipo": "Educaciónal",
                    "precio": 3800,
                    "costo": 1000,
                    "descuento_max": 65,
                    "tipo_descuento": "%"
                }
            ]
        },
        {
            "id": 2,
            "nombre": "UHL",
            "slug": "uhl",
            "productos": [
                {
                    "id": 1,
                    "nombre": "Producto de ejmplo 1",
                    "tipo": "Educaciónal",
                    "precio": 5000,
                    "costo": 3000,
                    "descuento_max": 65,
                    "tipo_descuento": "%"
                },
                {
                    "id": 2,
                    "nombre": "Producto de ejmplo 2",
                    "tipo": "Educaciónal",
                    "precio": 4000,
                    "costo": 2000,
                    "descuento_max": 65,
                    "tipo_descuento": "%"
                },
                {
                    "id": 3,
                    "nombre": "Producto de ejmplo 3",
                    "tipo": "Educaciónal",
                    "precio": 3000,
                    "costo": 1000,
                    "descuento_max": 65,
                    "tipo_descuento": "%"
                },
                {
                    "id": 4,
                    "nombre": "Producto de ejmplo 4",
                    "tipo": "Educaciónal",
                    "precio": 2000,
                    "costo": 1000,
                    "descuento_max": 65,
                    "tipo_descuento": "%"
                },
                {
                    "id": 5,
                    "nombre": "Producto de ejmplo 5",
                    "tipo": "Educaciónal",
                    "precio": 4000,
                    "costo": 3000,
                    "descuento_max": 65,
                    "tipo_descuento": "%"
                }
            ],
        }
    ],
    // "descuentos": [
    //     {
    //         "id": 1,
    //         "cantidad": 10,
    //         "tipo": "%",
    //     },
    //     {
    //         "id": 2,
    //         "cantidad": 20,
    //         "tipo": "%",
    //     },
    //     {
    //         "id": 3,
    //         "cantidad": 30,
    //         "tipo": "%",
    //     }
    // ],
    "pasarela": [
        {
            "id": 1,
            "nombre": "OpenPay",
            "slug": "openpay",
            "comision": 2.29,
            "comision_fija": 2.5,
            "iva": 16,
            "tipo_comision": "%"
        },
        {
            "id": 2,
            "nombre": "Paypal",
            "slug": "paypal",
            "comision": 2.34,
            "comision_fija": 4,
            "iva": 16,
            "tipo_comision": "%"
        },
        {
            "id": 3,
            "nombre": "MercadoPago",
            "slug": "mercadopago",
            "comision": 3.3,
            "comision_fija": 5,
            "iva": 16,
            "tipo_comision": "%"
        },
        {
            "id": 4,
            "nombre": "Efectivo",
            "slug": "efectivo",
            "comision": 0,
            "comision_fija": 0,
            "iva": 16,
            "tipo_comision": "$"
        },
    ],
}