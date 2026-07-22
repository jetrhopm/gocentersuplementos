@extends('layouts.app')

@php
    $titles = [
        'privacy' => 'Privacidad',
        'terms' => 'Terminos',
        'returns' => 'Devoluciones',
        'shipping' => 'Envios',
    ];

    $shippingCost = number_format((float) config('services.store.shipping_cost', 150), 0);
    $freeShippingFrom = number_format((float) config('services.store.free_shipping_from', 999), 0);
@endphp

@section('title', ($titles[$type] ?? 'Politica').' | '.config('app.name'))

@section('content')
<section class="quiet-band">
    <div class="container-page py-10">
        <span class="badge">Politicas</span>
        <h1 class="section-heading mt-3">{{ $titles[$type] ?? 'Politica' }}</h1>
        <p class="mt-3 max-w-2xl text-zinc-400">Informacion importante sobre tu compra, pago, entrega y atencion posterior al pedido.</p>
    </div>
</section>

<section class="container-page py-12">
    <div class="panel max-w-4xl p-6 sm:p-8">
        <div class="space-y-8 leading-7 text-zinc-300">
            @if($type === 'privacy')
                <div>
                    <h2 class="text-xl font-black uppercase text-white">Datos que solicitamos</h2>
                    <p class="mt-3">Para procesar un pedido podemos solicitar nombre completo, correo electronico, telefono, direccion de entrega, referencias de domicilio y notas relacionadas con el pedido.</p>
                </div>
                <div>
                    <h2 class="text-xl font-black uppercase text-white">Uso de la informacion</h2>
                    <p class="mt-3">La informacion se usa para confirmar tu compra, preparar el pedido, coordinar el envio, compartir actualizaciones del pedido y atender aclaraciones relacionadas con la compra.</p>
                </div>
                <div>
                    <h2 class="text-xl font-black uppercase text-white">Pagos</h2>
                    <p class="mt-3">Los pagos con tarjeta se procesan mediante Clip bajo estandares de seguridad para pagos en linea. En pagos por transferencia u OXXO, usamos la referencia o comprobante solo para validar el pedido.</p>
                </div>
                <div>
                    <h2 class="text-xl font-black uppercase text-white">Proteccion y conservacion</h2>
                    <p class="mt-3">Mantenemos controles razonables para proteger la informacion del pedido. Conservamos datos necesarios para seguimiento, soporte, facturacion interna, auditoria y cumplimiento de obligaciones aplicables.</p>
                </div>
                <div>
                    <h2 class="text-xl font-black uppercase text-white">Derechos del cliente</h2>
                    <p class="mt-3">Puedes solicitar correccion o aclaracion sobre tus datos de pedido por los canales de contacto publicados en la tienda. Para atenderte mejor, ten a la mano tu folio.</p>
                </div>
            @elseif($type === 'terms')
                <div>
                    <h2 class="text-xl font-black uppercase text-white">Compra y confirmacion</h2>
                    <p class="mt-3">Al realizar un pedido aceptas proporcionar informacion correcta para contacto y entrega. El pedido se considera confirmado cuando el pago es aprobado o validado por el administrador, segun el metodo elegido.</p>
                </div>
                <div>
                    <h2 class="text-xl font-black uppercase text-white">Precios, promociones y stock</h2>
                    <p class="mt-3">Los precios, descuentos, promociones y disponibilidad pueden cambiar sin previo aviso. Si un producto no esta disponible despues de recibir el pedido, te contactaremos para ofrecer una alternativa, ajuste o cancelacion.</p>
                </div>
                <div>
                    <h2 class="text-xl font-black uppercase text-white">Pagos</h2>
                    <p class="mt-3">Aceptamos transferencia bancaria, pago en OXXO y pago con Clip. En transferencia u OXXO, el pedido queda pendiente hasta validar el pago. En Clip, la confirmacion depende de la respuesta del proveedor de pago.</p>
                </div>
                <div>
                    <h2 class="text-xl font-black uppercase text-white">Envio y recepcion</h2>
                    <p class="mt-3">El cliente debe revisar que su direccion sea correcta antes de confirmar. Una vez enviado el paquete, los tiempos pueden variar por cobertura, volumen operativo o disponibilidad de la paqueteria.</p>
                </div>
                <div>
                    <h2 class="text-xl font-black uppercase text-white">Uso del sitio</h2>
                    <p class="mt-3">No esta permitido manipular precios, cantidades, inventario, formularios o procesos de pago. La tienda puede cancelar pedidos con informacion incompleta, pago no validado o actividad irregular.</p>
                </div>
            @elseif($type === 'returns')
                <div>
                    <h2 class="text-xl font-black uppercase text-white">Plazo para aclaraciones</h2>
                    <p class="mt-3">Aceptamos aclaraciones por producto danado, incompleto o incorrecto dentro de las primeras 48 horas posteriores a la entrega del pedido.</p>
                </div>
                <div>
                    <h2 class="text-xl font-black uppercase text-white">Condiciones del producto</h2>
                    <p class="mt-3">Los suplementos deben conservar sello, empaque y contenido original para cambios o devoluciones. Productos abiertos no son retornables, salvo defecto de fabrica o error comprobable en el envio.</p>
                </div>
                <div>
                    <h2 class="text-xl font-black uppercase text-white">Ropa y accesorios</h2>
                    <p class="mt-3">La ropa debe estar sin uso, sin lavar, con etiquetas y en empaque original. Los accesorios deben estar completos y sin senales de uso indebido.</p>
                </div>
                <div>
                    <h2 class="text-xl font-black uppercase text-white">Proceso de revision</h2>
                    <p class="mt-3">Para iniciar una aclaracion, comparte tu folio, descripcion del caso y fotografias claras del producto, empaque y guia. Revisaremos la informacion antes de autorizar cambio, reposicion o reembolso.</p>
                </div>
                <div>
                    <h2 class="text-xl font-black uppercase text-white">Costos de envio por devolucion</h2>
                    <p class="mt-3">Si el error fue de la tienda o paqueteria, te indicaremos el proceso sin costo adicional. Si el cambio es por eleccion del cliente, el costo de reenvio puede correr por cuenta del cliente.</p>
                </div>
            @else
                <div>
                    <h2 class="text-xl font-black uppercase text-white">Costo de envio</h2>
                    <p class="mt-3">El costo de envio es de ${{ $shippingCost }} MXN. Los pedidos mayores a ${{ $freeShippingFrom }} MXN tienen envio gratis dentro de la cobertura disponible.</p>
                </div>
                <div>
                    <h2 class="text-xl font-black uppercase text-white">Paqueterias</h2>
                    <p class="mt-3">Trabajamos con DHL, FedEx o Estafeta. La paqueteria se asigna segun la region de entrega, cobertura, disponibilidad operativa y mejor opcion logistica para el domicilio.</p>
                </div>
                <div>
                    <h2 class="text-xl font-black uppercase text-white">Tiempo estimado</h2>
                    <p class="mt-3">El tiempo estimado de entrega es de 2 a 3 dias habiles. Este plazo puede variar segun region, disponibilidad de paqueteria, volumen de envios, dias festivos o situaciones externas al servicio.</p>
                </div>
                <div>
                    <h2 class="text-xl font-black uppercase text-white">Seguro de envio</h2>
                    <p class="mt-3">Los envios estan asegurados en compras mayores de $1,500 MXN. En caso de incidencia con paqueteria, revisaremos el caso con el folio, guia y evidencia correspondiente.</p>
                </div>
                <div>
                    <h2 class="text-xl font-black uppercase text-white">Guia y seguimiento</h2>
                    <p class="mt-3">Cuando el pedido sea preparado y entregado a paqueteria, se agregara el numero de guia para seguimiento. El rastreo puede tardar algunas horas en reflejar movimiento en el sistema de la paqueteria.</p>
                </div>
                <div>
                    <h2 class="text-xl font-black uppercase text-white">Direccion de entrega</h2>
                    <p class="mt-3">Es responsabilidad del cliente capturar una direccion completa y correcta. Si la paqueteria reporta domicilio incorrecto, zona extendida o imposibilidad de entrega, te contactaremos para coordinar la mejor alternativa disponible.</p>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
