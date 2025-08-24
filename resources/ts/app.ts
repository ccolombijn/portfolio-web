import { headerBackgroundEffects } from "./app/headerBackgroundEffects";
import { aiClickwords } from "./app/ai/clickwords";
import { aiSummarize } from "./app/ai/summarize";
import { form } from "./app/form";
import { image } from "./app/image";
import { navResponsive } from "./app/navResponsive";
import.meta.glob([
    '../images/**',
]);
headerBackgroundEffects();
aiClickwords();
aiSummarize();
form();
//image();
navResponsive();
