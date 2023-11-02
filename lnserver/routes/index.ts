import express, { Application } from "express";
import { invoices } from "./invoices";

export function routes(app: Application) {
    app.use(express.json());
    app.use('/api/invoices', invoices);
}
