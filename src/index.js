const { registerPaymentMethod } = window.wc.wcBlocksRegistry;
const { createElement, useState, useEffect, useRef } = window.wp.element;

const zarinPalData = window.wc.wcSettings.getSetting('paymentMethodData')?.zarinpal;
const zarinPalGateName = wcZarinpalSettings?.gateName || 'zarinpal';

const ZarinPalPaymentMethod = (props) => {
    const { eventRegistration = {}, emitResponse } = props;
    const [isProcessing, setIsProcessing] = useState(false);
    const processingRef = useRef(false);

    useEffect(() => {
        if (!eventRegistration.onPaymentProcessing) {
            return;
        }

        const resetProcessing = () => {
            processingRef.current = false;
            setIsProcessing(false);
        };

        const unsubscribeValidation = eventRegistration.onCheckoutValidation?.(() => {
            resetProcessing();
        });

        const unsubscribeAfterError = eventRegistration.onCheckoutFail?.(() => {
            resetProcessing();
        });

        const unsubscribeProcessing = eventRegistration.onPaymentSetup(() => {
            if (!processingRef.current) {
                processingRef.current = true;
                setIsProcessing(true);
            }

            try {
                return {
                    type: 'success',
                    meta: {
                        paymentMethodData: {
                            payment_method: zarinPalGateName,
                            title: wcZarinpalSettings?.title,
                            description: wcZarinpalSettings?.description
                        },
                    },
                };
            } catch (error) {
                resetProcessing();
                return {
                    type: 'error',
                    message: error.message,
                };
            }
        });

        return () => {
            resetProcessing();
            if (typeof unsubscribeProcessing === 'function') {
                unsubscribeProcessing();
            }
            if (typeof unsubscribeValidation === 'function') {
                unsubscribeValidation();
            }
            if (typeof unsubscribeAfterError === 'function') {
                unsubscribeAfterError();
            }
        };
    }, [eventRegistration]);

    useEffect(() => {
        return () => {
            processingRef.current = false;
            setIsProcessing(false);
        };
    }, []);

    return createElement('div', { className: 'wc-zarinpal-payment-method' },
        createElement('div', { className: 'wc-zarinpal-description' },
            wcZarinpalSettings?.description || 'پرداخت امن به وسیله درگاه زرین‌پال'
        ),
        createElement('div', { className: 'wc-zarinpal-logo' },
            createElement('img', {
                src: wcZarinpalSettings?.logoUrl,
                alt: 'ZarinPal',
                style: {
                    height: '24px',
                    marginTop: '10px'
                }
            })
        ),
    );
};

const ZarinPalEdit = (props) => {
    return createElement(ZarinPalPaymentMethod, {
        ...props,
        isEdit: true
    });
};

registerPaymentMethod({
    name: zarinPalGateName,
    label: zarinPalData?.title || 'پرداخت امن زرین‌پال',
    content: createElement(ZarinPalPaymentMethod),
    edit: createElement(ZarinPalEdit),
    canMakePayment: () => true,
    ariaLabel: zarinPalData?.title || 'پرداخت با زرین‌پال',
    supports: {
        features: zarinPalData?.supports || ['products']
    },
});