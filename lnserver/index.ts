import express from "express";
import cors from "cors";
import { env } from "./env";
import { routes } from "./routes";

// TODO:
// [] - Extract endpoint from lib to env on client
// [] - Add snack with for user feedback
// [] - Emoji for channel page
// [] - relative imports

const app = express();

app.use(cors());
routes(app);

app.listen(env.PORT, () => console.log(`listening on port ${env.PORT}`));
