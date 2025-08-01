import { headerBackgroundEffects } from "./headerBackgroundEffects";
import { aiClickwords } from "./ai/clickwords";
import { aiSummarize } from "./ai/summarize";
import { form } from "./form";
import { image } from "./image";
import { navResponsive } from "./navResponsive";
import.meta.glob([
    '../images/**',
]);
headerBackgroundEffects();
aiClickwords();
aiSummarize();
form();
//image();
navResponsive();
