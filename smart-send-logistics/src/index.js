const { __ } = window.wp.i18n;
const { registerPlugin } = window.wp.plugins;
const { ExperimentalOrderShippingPackages } = window.wc.blocksCheckout;

const render = () => {
	return (
		<ExperimentalOrderMeta>
			<div>{ __( 'Express Shipping', 'YOUR-TEXTDOMAIN' ) }</div>
		</ExperimentalOrderMeta>
	);
};

registerPlugin( 'slot-and-fill-examples', {
	render,
	scope: 'woocommerce-checkout',
} );