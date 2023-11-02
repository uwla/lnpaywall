import { Request, Router } from "express";
import { createInvoice, getInvoice } from "lightning";
import { lnd } from "../lnd";

const router = Router();

router.post('/', async (req: InvoiceRequest, res) => {
    try {
        let amount : number = 10;
        if (req.body.amount)
            amount = req.body.amount;

        const invoice = await createInvoice({ lnd, tokens: amount });

        return res.send({ invoice });
    } catch (err) {
        return res.status(500).send(err);
    }
});

interface InvoiceRequest extends Request {
    body: {
        amount?: number;
    }
}

interface PayRequest extends Request {
    body: {
        invoiceId: string;
    }
}

router.post('/status', async (req: PayRequest, res) => {
    try {
        const invoiceId = req.body.invoiceId;

        const result = await getInvoice({ lnd, id: invoiceId });

        return res.send(result);
    } catch (err) {
        return res.status(500).send(err);
    }
});

export const invoices = router;
