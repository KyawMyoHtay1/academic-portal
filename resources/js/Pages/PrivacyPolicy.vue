<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);

const lastUpdated = new Date().toLocaleDateString("en-GB", {
    year: "numeric",
    month: "long",
    day: "numeric",
});

/**
 * TERMELY INTEGRATION
 *
 * This is the exact HTML embed you pasted from Termly's
 * "Add to website" → "HTML" option.
 *
 * Important:
 * - Only paste trusted HTML here (Termly is trusted).
 * - Do NOT allow end‑users to edit this HTML.
 */
const termlyHtml = `
<style>
  [data-custom-class='body'], [data-custom-class='body'] * {
          background: transparent !important;
        }
[data-custom-class='title'], [data-custom-class='title'] * {
          font-family: Arial !important;
font-size: 26px !important;
color: #000000 !important;
        }
[data-custom-class='subtitle'], [data-custom-class='subtitle'] * {
          font-family: Arial !important;
color: #595959 !important;
font-size: 14px !important;
        }
[data-custom-class='heading_1'], [data-custom-class='heading_1'] * {
          font-family: Arial !important;
font-size: 19px !important;
color: #000000 !important;
        }
[data-custom-class='heading_2'], [data-custom-class='heading_2'] * {
          font-family: Arial !important;
font-size: 17px !important;
color: #000000 !important;
        }
[data-custom-class='body_text'], [data-custom-class='body_text'] * {
          color: #595959 !important;
font-size: 14px !important;
font-family: Arial !important;
        }
[data-custom-class='link'], [data-custom-class='link'] * {
          color: #3030F1 !important;
font-size: 14px !important;
font-family: Arial !important;
word-break: break-word !important;
        }
</style>
      <span style="display: block;margin: 0 auto 3.125rem;width: 11.125rem;height: 2.375rem;background: url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNzgiIGhlaWdodD0iMzgiIHZpZXdCb3g9IjAgMCAxNzggMzgiPgogICAgPGcgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJldmVub2RkIj4KICAgICAgICA8cGF0aCBmaWxsPSIjRDFEMUQxIiBkPSJNNC4yODMgMjQuMTA3Yy0uNzA1IDAtMS4yNTgtLjI1Ni0xLjY2LS43NjhoLS4wODVjLjA1Ny41MDIuMDg2Ljc5Mi4wODYuODd2Mi40MzRILjk4NXYtOC42NDhoMS4zMzJsLjIzMS43NzloLjA3NmMuMzgzLS41OTQuOTUtLjg5MiAxLjcwMi0uODkyLjcxIDAgMS4yNjQuMjc0IDEuNjY1LjgyMi40MDEuNTQ4LjYwMiAxLjMwOS42MDIgMi4yODMgMCAuNjQtLjA5NCAxLjE5OC0uMjgyIDEuNjctLjE4OC40NzMtLjQ1Ni44MzMtLjgwMyAxLjA4LS4zNDcuMjQ3LS43NTYuMzctMS4yMjUuMzd6TTMuOCAxOS4xOTNjLS40MDUgMC0uNy4xMjQtLjg4Ni4zNzMtLjE4Ny4yNDktLjI4My42Ni0uMjkgMS4yMzN2LjE3N2MwIC42NDUuMDk1IDEuMTA3LjI4NyAxLjM4Ni4xOTIuMjguNDk1LjQxOS45MS40MTkuNzM0IDAgMS4xMDEtLjYwNSAxLjEwMS0xLjgxNiAwLS41OS0uMDktMS4wMzQtLjI3LTEuMzI5LS4xODItLjI5NS0uNDY1LS40NDMtLjg1Mi0uNDQzem01LjU3IDEuNzk0YzAgLjU5NC4wOTggMS4wNDQuMjkzIDEuMzQ4LjE5Ni4zMDQuNTEzLjQ1Ny45NTQuNDU3LjQzNyAwIC43NS0uMTUyLjk0Mi0uNDU0LjE5Mi0uMzAzLjI4OC0uNzUzLjI4OC0xLjM1MSAwLS41OTUtLjA5Ny0xLjA0LS4yOS0xLjMzOC0uMTk0LS4yOTctLjUxLS40NDUtLjk1LS40NDUtLjQzOCAwLS43NTMuMTQ3LS45NDYuNDQzLS4xOTQuMjk1LS4yOS43NDItLjI5IDEuMzR6bTQuMTUzIDBjMCAuOTc3LS4yNTggMS43NDItLjc3NCAyLjI5My0uNTE1LjU1Mi0xLjIzMy44MjctMi4xNTQuODI3LS41NzYgMC0xLjA4NS0uMTI2LTEuNTI1LS4zNzhhMi41MiAyLjUyIDAgMCAxLTEuMDE1LTEuMDg4Yy0uMjM3LS40NzMtLjM1NS0xLjAyNC0uMzU1LTEuNjU0IDAtLjk4MS4yNTYtMS43NDQuNzY4LTIuMjg4LjUxMi0uNTQ1IDEuMjMyLS44MTcgMi4xNi0uODE3LjU3NiAwIDEuMDg1LjEyNiAxLjUyNS4zNzYuNDQuMjUxLjc3OS42MSAxLjAxNSAxLjA4LjIzNi40NjkuMzU1IDEuMDE5LjM1NSAxLjY0OXpNMTkuNzEgMjRsLS40NjItMi4xLS42MjMtMi42NTNoLS4wMzdMMTcuNDkzIDI0SDE1LjczbC0xLjcwOC02LjAwNWgxLjYzM2wuNjkzIDIuNjU5Yy4xMS40NzYuMjI0IDEuMTMzLjMzOCAxLjk3MWguMDMyYy4wMTUtLjI3Mi4wNzctLjcwNC4xODgtMS4yOTRsLjA4Ni0uNDU3Ljc0Mi0yLjg3OWgxLjgwNGwuNzA0IDIuODc5Yy4wMTQuMDc5LjAzNy4xOTUuMDY3LjM1YTIwLjk5OCAyMC45OTggMCAwIDEgLjE2NyAxLjAwMmMuMDIzLjE2NS4wMzYuMjk5LjA0LjM5OWguMDMyYy4wMzItLjI1OC4wOS0uNjExLjE3Mi0xLjA2LjA4Mi0uNDUuMTQxLS43NTQuMTc3LS45MTFsLjcyLTIuNjU5aDEuNjA2TDIxLjQ5NCAyNGgtMS43ODN6bTcuMDg2LTQuOTUyYy0uMzQ4IDAtLjYyLjExLS44MTcuMzMtLjE5Ny4yMi0uMzEuNTMzLS4zMzguOTM3aDIuMjk5Yy0uMDA4LS40MDQtLjExMy0uNzE3LS4zMTctLjkzNy0uMjA0LS4yMi0uNDgtLjMzLS44MjctLjMzem0uMjMgNS4wNmMtLjk2NiAwLTEuNzIyLS4yNjctMi4yNjYtLjgtLjU0NC0uNTM0LS44MTYtMS4yOS0uODE2LTIuMjY3IDAtMS4wMDcuMjUxLTEuNzg1Ljc1NC0yLjMzNC41MDMtLjU1IDEuMTk5LS44MjUgMi4wODctLjgyNS44NDggMCAxLjUxLjI0MiAxLjk4Mi43MjUuNDcyLjQ4NC43MDkgMS4xNTIuNzA5IDIuMDA0di43OTVoLTMuODczYy4wMTguNDY1LjE1Ni44MjkuNDE0IDEuMDkuMjU4LjI2MS42Mi4zOTIgMS4wODUuMzkyLjM2MSAwIC43MDMtLjAzNyAxLjAyNi0uMTEzYTUuMTMzIDUuMTMzIDAgMCAwIDEuMDEtLjM2djEuMjY4Yy0uMjg3LjE0My0uNTkzLjI1LS45Mi4zMmE1Ljc5IDUuNzkgMCAwIDEtMS4xOTEuMTA0em03LjI1My02LjIyNmMuMjIyIDAgLjQwNi4wMTYuNTUzLjA0OWwtLjEyNCAxLjUzNmExLjg3NyAxLjg3NyAwIDAgMC0uNDgzLS4wNTRjLS41MjMgMC0uOTMuMTM0LTEuMjIyLjQwMy0uMjkyLjI2OC0uNDM4LjY0NC0uNDM4IDEuMTI4VjI0aC0xLjYzOHYtNi4wMDVoMS4yNGwuMjQyIDEuMDFoLjA4Yy4xODctLjMzNy40MzktLjYwOC43NTYtLjgxNGExLjg2IDEuODYgMCAwIDEgMS4wMzQtLjMwOXptNC4wMjkgMS4xNjZjLS4zNDcgMC0uNjIuMTEtLjgxNy4zMy0uMTk3LjIyLS4zMS41MzMtLjMzOC45MzdoMi4yOTljLS4wMDctLjQwNC0uMTEzLS43MTctLjMxNy0uOTM3LS4yMDQtLjIyLS40OC0uMzMtLjgyNy0uMzN6bS4yMyA1LjA2Yy0uOTY2IDAtMS43MjItLjI2Ny0yLjI2Ni0uOC0uNTQ0LS41MzQtLjgxNi0xLjI5LS44MTYtMi4yNjcgMC0xLjAwNy4yNTEtMS43ODUuNzU0LTIuMzM0LjUwNC0uNTUgMS4yLS44MjUgMi4wODctLjgyNS44NDkgMCAxLjUxLjI0MiAxLjk4Mi43MjUuNDczLjQ4NC43MDkgMS4xNTIuNzA5IDIuMDA0di43OTVoLTMuODczYy4wMTguNDY1LjE1Ni44MjkuNDE0IDEuMDkuMjU4LjI2MS42Mi4zOTIgMS4wODUuMzkyLjM2MiAwIC43MDQtLjAzNyAxLjAyNi0uMTEzYTUuMTMzIDUuMTMzIDAgMCAwIDEuMDEtLjM2djEuMjY4Yy0uMjg3LjE0My0uNTkzLjI1LS45MTkuMzJhNS43OSA1Ljc5IDAgMCAxLTEuMTkyLjEwNHptNS44MDMgMGMtLjcwNiAwLTEuMjYtLjI3NS0xLjY2My0uODIyLS40MDMtLjU0OC0uNjA0LTEuMzA3LS42MDQtMi4yNzggMC0uOTg0LjIwNS0xLjc1Mi42MTUtMi4zMDEuNDEtLjU1Ljk3NS0uODI1IDEuNjk1LS44MjUuNzU1IDAgMS4zMzIuMjk0IDEuNzI5Ljg4MWguMDU0YTYuNjk3IDYuNjk3IDAgMCAxLS4xMjQtMS4xOTh2LTEuOTIyaDEuNjQ0VjI0SDQ2LjQzbC0uMzE3LS43NzloLS4wN2MtLjM3Mi41OTEtLjk0Ljg4Ni0xLjcwMi44ODZ6bS41NzQtMS4zMDZjLjQyIDAgLjcyNi0uMTIxLjkyMS0uMzY1LjE5Ni0uMjQzLjMwMi0uNjU3LjMyLTEuMjR2LS4xNzhjMC0uNjQ0LS4xLTEuMTA2LS4yOTgtMS4zODYtLjE5OS0uMjc5LS41MjItLjQxOS0uOTctLjQxOWEuOTYyLjk2MiAwIDAgMC0uODUuNDY1Yy0uMjAzLjMxLS4zMDQuNzYtLjMwNCAxLjM1IDAgLjU5Mi4xMDIgMS4wMzUuMzA2IDEuMzMuMjA0LjI5Ni40OTYuNDQzLjg3NS40NDN6bTEwLjkyMi00LjkyYy43MDkgMCAxLjI2NC4yNzcgMS42NjUuODMuNC41NTMuNjAxIDEuMzEyLjYwMSAyLjI3NSAwIC45OTItLjIwNiAxLjc2LS42MiAyLjMwNC0uNDE0LjU0NC0uOTc3LjgxNi0xLjY5LjgxNi0uNzA1IDAtMS4yNTgtLjI1Ni0xLjY1OS0uNzY4aC0uMTEzbC0uMjc0LjY2MWgtMS4yNTF2LTguMzU3aDEuNjM4djEuOTQ0YzAgLjI0Ny0uMDIxLjY0My0uMDY0IDEuMTg3aC4wNjRjLjM4My0uNTk0Ljk1LS44OTIgMS43MDMtLjg5MnptLS41MjcgMS4zMWMtLjQwNCAwLS43LjEyNS0uODg2LjM3NC0uMTg2LjI0OS0uMjgzLjY2LS4yOSAxLjIzM3YuMTc3YzAgLjY0NS4wOTYgMS4xMDcuMjg3IDEuMzg2LjE5Mi4yOC40OTUuNDE5LjkxLjQxOS4zMzcgMCAuNjA1LS4xNTUuODA0LS40NjUuMTk5LS4zMS4yOTgtLjc2LjI5OC0xLjM1IDAtLjU5MS0uMS0xLjAzNS0uMy0xLjMzYS45NDMuOTQzIDAgMCAwLS44MjMtLjQ0M3ptMy4xODYtMS4xOTdoMS43OTRsMS4xMzQgMy4zNzljLjA5Ni4yOTMuMTYzLjY0LjE5OCAxLjA0MmguMDMzYy4wMzktLjM3LjExNi0uNzE3LjIzLTEuMDQybDEuMTEyLTMuMzc5aDEuNzU3bC0yLjU0IDYuNzczYy0uMjM0LjYyNy0uNTY2IDEuMDk2LS45OTcgMS40MDctLjQzMi4zMTItLjkzNi40NjgtMS41MTIuNDY4LS4yODMgMC0uNTYtLjAzLS44MzMtLjA5MnYtMS4zYTIuOCAyLjggMCAwIDAgLjY0NS4wN2MuMjkgMCAuNTQzLS4wODguNzYtLjI2Ni4yMTctLjE3Ny4zODYtLjQ0NC41MDgtLjgwM2wuMDk2LS4yOTUtMi4zODUtNS45NjJ6Ii8+CiAgICAgICAgPGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNzMpIj4KICAgICAgICAgICAgPGNpcmNsZSBjeD0iMTkiIGN5PSIxOSIgcj0iMTkiIGZpbGw9IiNFMEUwRTAiLz4KICAgICAgICAgICAgPHBhdGggZmlsbD0iI0ZGRiIgZD0iTTIyLjQ3NCAxNS40NDNoNS4xNjJMMTIuNDM2IDMwLjRWMTAuMzYzaDE1LjJsLTUuMTYyIDUuMDh6Ii8+CiAgICAgICAgPC9nPgogICAgICAgIDxwYXRoIGZpbGw9IiNEMkQyRDIiIGQ9Ik0xMjEuNTQ0IDE0LjU2di0xLjcyOGg4LjI3MnYxLjcyOGgtMy4wMjRWMjRoLTIuMjR2LTkuNDRoLTMuMDA4em0xMy43NDQgOS41NjhjLTEuMjkgMC0yLjM0MS0uNDE5LTMuMTUyLTEuMjU2LS44MS0uODM3LTEuMjE2LTEuOTQ0LTEuMjE2LTMuMzJzLjQwOC0yLjQ3NyAxLjIyNC0zLjMwNGMuODE2LS44MjcgMS44NzItMS4yNCAzLjE2OC0xLjI0czIuMzYuNDAzIDMuMTkyIDEuMjA4Yy44MzIuODA1IDEuMjQ4IDEuODggMS4yNDggMy4yMjQgMCAuMzEtLjAyMS41OTctLjA2NC44NjRoLTYuNDY0Yy4wNTMuNTc2LjI2NyAxLjA0LjY0IDEuMzkyLjM3My4zNTIuODQ4LjUyOCAxLjQyNC41MjguNzc5IDAgMS4zNTUtLjMyIDEuNzI4LS45NmgyLjQzMmEzLjg5MSAzLjg5MSAwIDAgMS0xLjQ4OCAyLjA2NGMtLjczNi41MzMtMS42MjcuOC0yLjY3Mi44em0xLjQ4LTYuNjg4Yy0uNC0uMzUyLS44ODMtLjUyOC0xLjQ0OC0uNTI4cy0xLjAzNy4xNzYtMS40MTYuNTI4Yy0uMzc5LjM1Mi0uNjA1LjgyMS0uNjggMS40MDhoNC4xOTJjLS4wMzItLjU4Ny0uMjQ4LTEuMDU2LS42NDgtMS40MDh6bTcuMDE2LTIuMzA0djEuNTY4Yy41OTctMS4xMyAxLjQ2MS0xLjY5NiAyLjU5Mi0xLjY5NnYyLjMwNGgtLjU2Yy0uNjcyIDAtMS4xNzkuMTY4LTEuNTIuNTA0LS4zNDEuMzM2LS41MTIuOTE1LS41MTIgMS43MzZWMjRoLTIuMjU2di04Ljg2NGgyLjI1NnptNi40NDggMHYxLjMyOGMuNTY1LS45NyAxLjQ4My0xLjQ1NiAyLjc1Mi0xLjQ1Ni42NzIgMCAxLjI3Mi4xNTUgMS44LjQ2NC41MjguMzEuOTM2Ljc1MiAxLjIyNCAxLjMyOC4zMS0uNTU1LjczMy0uOTkyIDEuMjcyLTEuMzEyYTMuNDg4IDMuNDg4IDAgMCAxIDEuODE2LS40OGMxLjA1NiAwIDEuOTA3LjMzIDIuNTUyLjk5Mi42NDUuNjYxLjk2OCAxLjU5Ljk2OCAyLjc4NFYyNGgtMi4yNHYtNC44OTZjMC0uNjkzLS4xNzYtMS4yMjQtLjUyOC0xLjU5Mi0uMzUyLS4zNjgtLjgzMi0uNTUyLTEuNDQtLjU1MnMtMS4wOS4xODQtMS40NDguNTUyYy0uMzU3LjM2OC0uNTM2Ljg5OS0uNTM2IDEuNTkyVjI0aC0yLjI0di00Ljg5NmMwLS42OTMtLjE3Ni0xLjIyNC0uNTI4LTEuNTkyLS4zNTItLjM2OC0uODMyLS41NTItMS40NC0uNTUycy0xLjA5LjE4NC0xLjQ0OC41NTJjLS4zNTcuMzY4LS41MzYuODk5LS41MzYgMS41OTJWMjRoLTIuMjU2di04Ljg2NGgyLjI1NnpNMTY0LjkzNiAyNFYxMi4xNmgyLjI1NlYyNGgtMi4yNTZ6bTcuMDQtLjE2bC0zLjQ3Mi04LjcwNGgyLjUyOGwyLjI1NiA2LjMwNCAyLjM4NC02LjMwNGgyLjM1MmwtNS41MzYgMTMuMDU2aC0yLjM1MmwxLjg0LTQuMzUyeiIvPgogICAgPC9nPgo8L3N2Zz4K) center no-repeat;"></span>

      <div data-custom-class="body">
      <div><strong><span style="font-size: 26px;"><span data-custom-class="title"><bdt class="block-component"></bdt><bdt class="question"><h1>PRIVACY POLICY</h1></bdt><bdt class="statement-end-if-in-editor"></bdt></span></span></strong></div><div><span style="color: rgb(127, 127, 127);"><strong><span style="font-size: 15px;"><span data-custom-class="subtitle">Last updated <bdt class="question">January 26, 2026</bdt></span></span></strong></span></div><div><br></div><div><br></div><div><br></div><div style="line-height: 1.5;"><span style="color: rgb(127, 127, 127);"><span style="color: rgb(89, 89, 89); font-size: 15px;"><span data-custom-class="body_text">This Privacy Notice for <bdt class="question noTranslate">University Academic Portal</bdt><bdt class="block-component"></bdt></bdt> (<bdt class="block-component"></bdt>'<strong>we</strong>', '<strong>us</strong>', or '<strong>our</strong>'<bdt class="else-block"></bdt></span><span data-custom-class="body_text">), describes how and why we might access, collect, store, use, and/or share (<bdt class="block-component"></bdt>'<strong>process</strong>'<bdt class="else-block"></bdt>) your personal information when you use our services (<bdt class="block-component"></bdt>'<strong>Services</strong>'<bdt class="else-block"></bdt>), including when you:</span></span></span><span style="font-size: 15px;"><span style="color: rgb(127, 127, 127);"><span data-custom-class="body_text"><span style="color: rgb(89, 89, 89);"><span data-custom-class="body_text"><bdt class="block-component"></bdt></span></span></span></span></span></div><ul><li data-custom-class="body_text" style="line-height: 1.5;"><span style="font-size: 15px; color: rgb(89, 89, 89);"><span style="font-size: 15px; color: rgb(89, 89, 89);"><span data-custom-class="body_text">Visit our website<bdt class="block-component"></bdt> at <span style="color: rgb(0, 58, 250);"><bdt class="question noTranslate"><a target="_blank" data-custom-class="link" href="http://127.0.0.1:8000">http://127.0.0.1:8000</a></bdt></span><span style="font-size: 15px;"><span style="color: rgb(89, 89, 89);"><span data-custom-class="body_text"><span style="font-size: 15px;"><span style="color: rgb(89, 89, 89);"><bdt class="statement-end-if-in-editor"> or any website of ours that links to this Privacy Notice</bdt></span></span></span></span></span></span></span></span></li></ul><div><bdt class="block-component"><span style="font-size: 15px;"><span style="font-size: 15px;"><span style="color: rgb(127, 127, 127);"><span data-custom-class="body_text"><span style="color: rgb(89, 89, 89);"><span data-custom-class="body_text"><bdt class="block-component"></bdt></bdt></span></span></span></span></span></span></span></span></li></ul><div style="line-height: 1.5;"><bdt class="block-component"><span style="font-size: 15px;"></span></bdt></div><ul><li data-custom-class="body_text" style="line-height: 1.5;"><span style="font-size: 15px;">Use <bdt class="question">University Academic Portal</bdt>. <bdt class="question">__________</bdt></span><bdt class="statement-end-if-in-editor"><span style="font-size: 15px;"></span></bdt></li></ul><div style="line-height: 1.5;"><span style="font-size: 15px;"><span style="color: rgb(127, 127, 127);"><span data-custom-class="body_text"><span style="color: rgb(89, 89, 89);"><span data-custom-class="body_text"><bdt class="block-component"></bdt></span></span></span></span></span></div><ul><li data-custom-class="body_text" style="line-height: 1.5;"><span style="font-size: 15px; color: rgb(89, 89, 89);"><span style="font-size: 15px; color: rgb(89, 89, 89);"><span data-custom-class="body_text">Engage with us in other related ways, including any marketing or events<span style="font-size: 15px;"><span style="color: rgb(89, 89, 89);"><span data-custom-class="body_text"><span style="font-size: 15px;"><span style="color: rgb(89, 89, 89);"><bdt class="statement-end-if-in-editor"></bdt></span></span></span></span></span></span></span></span></li></ul><div style="line-height: 1.5;"><span style="font-size: 15px;"><span style="color: rgb(127, 127, 127);"><span data-custom-class="body_text"><strong>Questions or concerns? </strong>Reading this Privacy Notice will help you understand your privacy rights and choices. We are responsible for making decisions about how your personal information is processed. If you do not agree with our policies and practices, please do not use our Services.<bdt class="block-component"></span></span></span></div>
<!-- Full Termly HTML continues ... -->
`;
</script>

<template>
    <Head title="Privacy Policy" />

    <!-- Authenticated users see the policy inside the main dashboard layout -->
    <AuthenticatedLayout v-if="isAuthenticated">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                Privacy Policy
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Privacy Policy' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="portal-card p-8">
                    <div class="prose prose-slate max-w-none">
                        <h1 class="text-3xl font-bold text-slate-900 mb-2">
                            Privacy Policy
                        </h1>
                        <p class="text-sm text-slate-600 mb-6">
                            Last updated: {{ lastUpdated }}
                        </p>
                        <p class="text-slate-700 mb-6">
                            This page displays the official Privacy Policy for the
                            University Academic Portal, powered by
                            <span class="font-semibold">Termly</span>.
                        </p>

                        <!-- Termly HTML embed -->
                        <div
                            v-if="termlyHtml && termlyHtml.trim().length"
                            class="not-prose border border-slate-200 rounded-xl p-4 bg-white"
                            v-html="termlyHtml"
                        />

                        <!-- Admin-only helper state (shows until you paste the Termly HTML) -->
                        <div
                            v-else
                            class="not-prose border border-dashed border-amber-400 bg-amber-50 rounded-xl p-4 text-sm text-amber-800"
                        >
                            <p class="font-semibold mb-1">Admin setup required</p>
                            <p>
                                In Termly, open your Privacy Policy →
                                <span class="font-semibold">Add to website</span> →
                                choose <span class="font-semibold">HTML</span> →
                                copy the full embed and paste it into the
                                <code>termlyHtml</code> string in
                                <code>resources/js/Pages/PrivacyPolicy.vue</code>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <!-- Guests see the same Termly policy inside the public layout -->
    <GuestLayout v-else>
        <div class="w-full max-w-4xl px-6 py-8 sm:px-8">
            <div class="portal-card p-8">
                <div class="prose prose-slate max-w-none">
                    <h1 class="text-3xl font-bold text-slate-900 mb-2">
                        Privacy Policy
                    </h1>
                    <p class="text-sm text-slate-600 mb-6">
                        Last updated: {{ lastUpdated }}
                    </p>
                    <p class="text-slate-700 mb-6">
                        This page displays the official Privacy Policy for the
                        University Academic Portal, powered by
                        <span class="font-semibold">Termly</span>.
                    </p>

                    <!-- Termly HTML embed -->
                    <div
                        v-if="termlyHtml && termlyHtml.trim().length"
                        class="not-prose border border-slate-200 rounded-xl p-4 bg-white"
                        v-html="termlyHtml"
                    />

                    <!-- Admin-only helper state (shows until you paste the Termly HTML) -->
                    <div
                        v-else
                        class="not-prose border border-dashed border-amber-400 bg-amber-50 rounded-xl p-4 text-sm text-amber-800"
                    >
                        <p class="font-semibold mb-1">Admin setup required</p>
                        <p>
                            In Termly, open your Privacy Policy →
                            <span class="font-semibold">Add to website</span> →
                            choose <span class="font-semibold">HTML</span> →
                            copy the full embed and paste it into the
                            <code>termlyHtml</code> string in
                            <code>resources/js/Pages/PrivacyPolicy.vue</code>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);

const lastUpdated = new Date().toLocaleDateString('en-GB', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
});
</script>

<template>
    <Head title="Privacy Policy" />

    <AuthenticatedLayout v-if="isAuthenticated">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                Privacy Policy
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Privacy Policy' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="portal-card p-8">
                    <div class="prose prose-slate max-w-none">
                        <h1 class="text-3xl font-bold text-slate-900 mb-2">Privacy Policy</h1>
                        <p class="text-sm text-slate-600 mb-8">
                            Last updated: {{ lastUpdated }}
                        </p>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">1. Introduction</h2>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                Welcome to the University Academic Portal ("we," "our," or "us"). We are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our academic portal.
                            </p>
                            <p class="text-slate-700 leading-relaxed">
                                By accessing or using our portal, you agree to the collection and use of information in accordance with this policy. If you do not agree with our policies and practices, please do not use our services.
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">2. Information We Collect</h2>
                            
                            <h3 class="text-xl font-semibold text-slate-800 mb-3">2.1 Personal Information</h3>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                We collect information that you provide directly to us, including:
                            </p>
                            <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                                <li>Name, email address, and contact information</li>
                                <li>Student identification numbers and academic records</li>
                                <li>Course enrollment and attendance data</li>
                                <li>Grades, assignments, and academic performance</li>
                                <li>Fee payment information and transaction records</li>
                                <li>Profile photos and identification documents</li>
                                <li>Emergency contact information</li>
                            </ul>

                            <h3 class="text-xl font-semibold text-slate-800 mb-3">2.2 Automatically Collected Information</h3>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                When you use our portal, we automatically collect certain information, including:
                            </p>
                            <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                                <li>IP address and device information</li>
                                <li>Browser type and version</li>
                                <li>Pages visited and time spent on pages</li>
                                <li>Login timestamps and session data</li>
                                <li>System logs and error reports</li>
                            </ul>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">3. How We Use Your Information</h2>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                We use the collected information for the following purposes:
                            </p>
                            <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                                <li><strong>Academic Management:</strong> To manage course enrollments, track attendance, record grades, and maintain academic records</li>
                                <li><strong>Communication:</strong> To send notifications, announcements, and important updates about your academic progress</li>
                                <li><strong>Fee Processing:</strong> To process fee payments and maintain financial records</li>
                                <li><strong>System Security:</strong> To protect against fraud, unauthorized access, and ensure system integrity</li>
                                <li><strong>Service Improvement:</strong> To analyze usage patterns and improve our portal's functionality</li>
                                <li><strong>Compliance:</strong> To comply with legal obligations and institutional policies</li>
                            </ul>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">4. Data Sharing and Disclosure</h2>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                We do not sell, trade, or rent your personal information to third parties. We may share your information only in the following circumstances:
                            </p>
                            <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                                <li><strong>Authorized Personnel:</strong> With university staff, faculty, and administrators who need access to perform their duties</li>
                                <li><strong>Service Providers:</strong> With trusted third-party service providers who assist in operating our portal (e.g., payment processors, hosting services)</li>
                                <li><strong>Legal Requirements:</strong> When required by law, court order, or government regulation</li>
                                <li><strong>Institutional Needs:</strong> For academic research, accreditation, or institutional reporting (with appropriate anonymization)</li>
                                <li><strong>Emergency Situations:</strong> To protect the safety and security of students, staff, and the institution</li>
                            </ul>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">5. Data Security</h2>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                We implement appropriate technical and organizational security measures to protect your personal information, including:
                            </p>
                            <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                                <li>Encryption of sensitive data in transit and at rest</li>
                                <li>Secure authentication and access controls</li>
                                <li>Regular security audits and vulnerability assessments</li>
                                <li>Employee training on data protection and privacy</li>
                                <li>Incident response procedures for data breaches</li>
                            </ul>
                            <p class="text-slate-700 leading-relaxed">
                                However, no method of transmission over the Internet or electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your information, we cannot guarantee absolute security.
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">6. Your Rights and Choices</h2>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                You have certain rights regarding your personal information:
                            </p>
                            <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                                <li><strong>Access:</strong> Request access to your personal information held by us</li>
                                <li><strong>Correction:</strong> Request correction of inaccurate or incomplete information</li>
                                <li><strong>Deletion:</strong> Request deletion of your information (subject to legal and institutional retention requirements)</li>
                                <li><strong>Objection:</strong> Object to certain processing of your information</li>
                                <li><strong>Data Portability:</strong> Request a copy of your data in a portable format</li>
                                <li><strong>Withdrawal of Consent:</strong> Withdraw consent for processing where applicable</li>
                            </ul>
                            <p class="text-slate-700 leading-relaxed">
                                To exercise these rights, please contact us using the information provided in the "Contact Us" section below.
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">7. Cookies and Tracking Technologies</h2>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                We use cookies and similar tracking technologies to enhance your experience on our portal. Cookies are small data files stored on your device that help us:
                            </p>
                            <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                                <li>Maintain your login session</li>
                                <li>Remember your preferences and settings</li>
                                <li>Analyze portal usage and performance</li>
                                <li>Provide security features (e.g., reCAPTCHA)</li>
                            </ul>
                            <p class="text-slate-700 leading-relaxed">
                                You can control cookies through your browser settings. However, disabling cookies may affect the functionality of our portal.
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">8. Third-Party Services</h2>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                Our portal may integrate with third-party services, including:
                            </p>
                            <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                                <li><strong>Payment Processors:</strong> Stripe for fee payments (see Stripe's privacy policy)</li>
                                <li><strong>Email Services:</strong> For sending notifications and communications</li>
                                <li><strong>Translation Services:</strong> Google Translate for language translation</li>
                                <li><strong>Security Services:</strong> Google reCAPTCHA for bot protection</li>
                            </ul>
                            <p class="text-slate-700 leading-relaxed">
                                These services have their own privacy policies. We encourage you to review them to understand how your information is handled by these third parties.
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">9. Data Retention</h2>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                We retain your personal information for as long as necessary to fulfill the purposes outlined in this policy, unless a longer retention period is required or permitted by law. Academic records are typically retained in accordance with institutional policies and legal requirements, which may extend beyond your enrollment period.
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">10. Children's Privacy</h2>
                            <p class="text-slate-700 leading-relaxed">
                                Our portal is intended for use by university students, faculty, and staff. We do not knowingly collect personal information from individuals under the age of 18 without appropriate parental or guardian consent, in accordance with applicable laws.
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">11. International Data Transfers</h2>
                            <p class="text-slate-700 leading-relaxed">
                                Your information may be transferred to and processed in countries other than your country of residence. We ensure that appropriate safeguards are in place to protect your information in accordance with this Privacy Policy and applicable data protection laws.
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">12. Changes to This Privacy Policy</h2>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                We may update this Privacy Policy from time to time to reflect changes in our practices or for legal, operational, or regulatory reasons. We will notify you of any material changes by:
                            </p>
                            <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                                <li>Posting the updated policy on this page</li>
                                <li>Updating the "Last updated" date</li>
                                <li>Sending notifications through the portal for significant changes</li>
                            </ul>
                            <p class="text-slate-700 leading-relaxed">
                                Your continued use of the portal after such changes constitutes acceptance of the updated policy.
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">13. Contact Us</h2>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                If you have questions, concerns, or requests regarding this Privacy Policy or our data practices, please contact us:
                            </p>
                            <div class="bg-slate-50 rounded-lg p-4 mb-4">
                                <p class="text-slate-700 font-semibold mb-2">University Academic Portal</p>
                                <p class="text-slate-700 mb-1">Email: privacy@university.edu</p>
                                <p class="text-slate-700 mb-1">Phone: +123 456 7890</p>
                                <p class="text-slate-700">Address: 123 University Avenue, City, Country</p>
                            </div>
                            <p class="text-slate-700 leading-relaxed">
                                For data protection inquiries, you may also contact our Data Protection Officer at: <strong>dpo@university.edu</strong>
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">14. Governing Law</h2>
                            <p class="text-slate-700 leading-relaxed">
                                This Privacy Policy is governed by and construed in accordance with the laws of the jurisdiction in which the university operates. Any disputes arising from this policy will be subject to the exclusive jurisdiction of the courts in that jurisdiction.
                            </p>
                        </section>

                        <div class="mt-8 pt-6 border-t border-slate-200">
                            <p class="text-sm text-slate-600">
                                By using the University Academic Portal, you acknowledge that you have read and understood this Privacy Policy.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <GuestLayout v-else>
        <div class="w-full max-w-4xl px-6 py-8 sm:px-8">
            <div class="portal-card p-8">
                <div class="prose prose-slate max-w-none">
                    <h1 class="text-3xl font-bold text-slate-900 mb-2">Privacy Policy</h1>
                    <p class="text-sm text-slate-600 mb-8">
                        Last updated: {{ lastUpdated }}
                    </p>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">1. Introduction</h2>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            Welcome to the University Academic Portal ("we," "our," or "us"). We are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our academic portal.
                        </p>
                        <p class="text-slate-700 leading-relaxed">
                            By accessing or using our portal, you agree to the collection and use of information in accordance with this policy. If you do not agree with our policies and practices, please do not use our services.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">2. Information We Collect</h2>
                        
                        <h3 class="text-xl font-semibold text-slate-800 mb-3">2.1 Personal Information</h3>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            We collect information that you provide directly to us, including:
                        </p>
                        <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                            <li>Name, email address, and contact information</li>
                            <li>Student identification numbers and academic records</li>
                            <li>Course enrollment and attendance data</li>
                            <li>Grades, assignments, and academic performance</li>
                            <li>Fee payment information and transaction records</li>
                            <li>Profile photos and identification documents</li>
                            <li>Emergency contact information</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-slate-800 mb-3">2.2 Automatically Collected Information</h3>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            When you use our portal, we automatically collect certain information, including:
                        </p>
                        <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                            <li>IP address and device information</li>
                            <li>Browser type and version</li>
                            <li>Pages visited and time spent on pages</li>
                            <li>Login timestamps and session data</li>
                            <li>System logs and error reports</li>
                        </ul>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">3. How We Use Your Information</h2>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            We use the collected information for the following purposes:
                        </p>
                        <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                            <li><strong>Academic Management:</strong> To manage course enrollments, track attendance, record grades, and maintain academic records</li>
                            <li><strong>Communication:</strong> To send notifications, announcements, and important updates about your academic progress</li>
                            <li><strong>Fee Processing:</strong> To process fee payments and maintain financial records</li>
                            <li><strong>System Security:</strong> To protect against fraud, unauthorized access, and ensure system integrity</li>
                            <li><strong>Service Improvement:</strong> To analyze usage patterns and improve our portal's functionality</li>
                            <li><strong>Compliance:</strong> To comply with legal obligations and institutional policies</li>
                        </ul>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">4. Data Sharing and Disclosure</h2>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            We do not sell, trade, or rent your personal information to third parties. We may share your information only in the following circumstances:
                        </p>
                        <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                            <li><strong>Authorized Personnel:</strong> With university staff, faculty, and administrators who need access to perform their duties</li>
                            <li><strong>Service Providers:</strong> With trusted third-party service providers who assist in operating our portal (e.g., payment processors, hosting services)</li>
                            <li><strong>Legal Requirements:</strong> When required by law, court order, or government regulation</li>
                            <li><strong>Institutional Needs:</strong> For academic research, accreditation, or institutional reporting (with appropriate anonymization)</li>
                            <li><strong>Emergency Situations:</strong> To protect the safety and security of students, staff, and the institution</li>
                        </ul>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">5. Data Security</h2>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            We implement appropriate technical and organizational security measures to protect your personal information, including:
                        </p>
                        <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                            <li>Encryption of sensitive data in transit and at rest</li>
                            <li>Secure authentication and access controls</li>
                            <li>Regular security audits and vulnerability assessments</li>
                            <li>Employee training on data protection and privacy</li>
                            <li>Incident response procedures for data breaches</li>
                        </ul>
                        <p class="text-slate-700 leading-relaxed">
                            However, no method of transmission over the Internet or electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your information, we cannot guarantee absolute security.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">6. Your Rights and Choices</h2>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            You have certain rights regarding your personal information:
                        </p>
                        <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                            <li><strong>Access:</strong> Request access to your personal information held by us</li>
                            <li><strong>Correction:</strong> Request correction of inaccurate or incomplete information</li>
                            <li><strong>Deletion:</strong> Request deletion of your information (subject to legal and institutional retention requirements)</li>
                            <li><strong>Objection:</strong> Object to certain processing of your information</li>
                            <li><strong>Data Portability:</strong> Request a copy of your data in a portable format</li>
                            <li><strong>Withdrawal of Consent:</strong> Withdraw consent for processing where applicable</li>
                        </ul>
                        <p class="text-slate-700 leading-relaxed">
                            To exercise these rights, please contact us using the information provided in the "Contact Us" section below.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">7. Cookies and Tracking Technologies</h2>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            We use cookies and similar tracking technologies to enhance your experience on our portal. Cookies are small data files stored on your device that help us:
                        </p>
                        <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                            <li>Maintain your login session</li>
                            <li>Remember your preferences and settings</li>
                            <li>Analyze portal usage and performance</li>
                            <li>Provide security features (e.g., reCAPTCHA)</li>
                        </ul>
                        <p class="text-slate-700 leading-relaxed">
                            You can control cookies through your browser settings. However, disabling cookies may affect the functionality of our portal.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">8. Third-Party Services</h2>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            Our portal may integrate with third-party services, including:
                        </p>
                        <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                            <li><strong>Payment Processors:</strong> Stripe for fee payments (see Stripe's privacy policy)</li>
                            <li><strong>Email Services:</strong> For sending notifications and communications</li>
                            <li><strong>Translation Services:</strong> Google Translate for language translation</li>
                            <li><strong>Security Services:</strong> Google reCAPTCHA for bot protection</li>
                        </ul>
                        <p class="text-slate-700 leading-relaxed">
                            These services have their own privacy policies. We encourage you to review them to understand how your information is handled by these third parties.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">9. Data Retention</h2>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            We retain your personal information for as long as necessary to fulfill the purposes outlined in this policy, unless a longer retention period is required or permitted by law. Academic records are typically retained in accordance with institutional policies and legal requirements, which may extend beyond your enrollment period.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">10. Children's Privacy</h2>
                        <p class="text-slate-700 leading-relaxed">
                            Our portal is intended for use by university students, faculty, and staff. We do not knowingly collect personal information from individuals under the age of 18 without appropriate parental or guardian consent, in accordance with applicable laws.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">11. International Data Transfers</h2>
                        <p class="text-slate-700 leading-relaxed">
                            Your information may be transferred to and processed in countries other than your country of residence. We ensure that appropriate safeguards are in place to protect your information in accordance with this Privacy Policy and applicable data protection laws.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">12. Changes to This Privacy Policy</h2>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            We may update this Privacy Policy from time to time to reflect changes in our practices or for legal, operational, or regulatory reasons. We will notify you of any material changes by:
                        </p>
                        <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                            <li>Posting the updated policy on this page</li>
                            <li>Updating the "Last updated" date</li>
                            <li>Sending notifications through the portal for significant changes</li>
                        </ul>
                        <p class="text-slate-700 leading-relaxed">
                            Your continued use of the portal after such changes constitutes acceptance of the updated policy.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">13. Contact Us</h2>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            If you have questions, concerns, or requests regarding this Privacy Policy or our data practices, please contact us:
                        </p>
                        <div class="bg-slate-50 rounded-lg p-4 mb-4">
                            <p class="text-slate-700 font-semibold mb-2">University Academic Portal</p>
                            <p class="text-slate-700 mb-1">Email: privacy@university.edu</p>
                            <p class="text-slate-700 mb-1">Phone: +123 456 7890</p>
                            <p class="text-slate-700">Address: 123 University Avenue, City, Country</p>
                        </div>
                        <p class="text-slate-700 leading-relaxed">
                            For data protection inquiries, you may also contact our Data Protection Officer at: <strong>dpo@university.edu</strong>
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">14. Governing Law</h2>
                        <p class="text-slate-700 leading-relaxed">
                            This Privacy Policy is governed by and construed in accordance with the laws of the jurisdiction in which the university operates. Any disputes arising from this policy will be subject to the exclusive jurisdiction of the courts in that jurisdiction.
                        </p>
                    </section>

                    <div class="mt-8 pt-6 border-t border-slate-200">
                        <p class="text-sm text-slate-600">
                            By using the University Academic Portal, you acknowledge that you have read and understood this Privacy Policy.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
