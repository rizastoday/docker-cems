const notifTelegram =
  document.querySelector("meta[name=baseURL]").content + "/testelegram";
// Vue.component('multiselect', window.VueMultiselect.default)
Vue.component("loading", {
  template: `
        <div class="position-fixed w-100 h-100 d-flex flex-column" style="z-index: 9999; top: 0; left: 0; background-color: rgba(0, 0, 0, 0.72)" v-if="show">
            <div class="loadingio-spinner-wedges-zbfkd16aas m-auto">
                <div class="ldio-7g70z2uxi2">
                    <div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div></div>
                </div>
            </div>
        </div>
    `,
  props: {
    show: {
      type: Boolean,
      required: true,
    },
  },
});
Vue.prototype.$moment = moment;
const theme = VueCompositionAPI.ref("light");
Vue.mixin({
  data: () => ({
    connection: null,
    connectionData: [],
    baseURL: document.querySelector("meta[name=baseURL]").content,
    bc_baseURL: document.querySelector("meta[name=bc_baseURL]").content,
    control: {
      loading: false,
      sidebar: document.querySelector("#sidebar")
        ? document
            .querySelector("#sidebar")
            .classList.contains("c-sidebar-show") ||
          document
            .querySelector("#sidebar")
            .classList.contains("c-sidebar-lg-show")
        : null,
    },
    master: {
      wsURL: {
        URL: document.querySelector("meta[name=websocket]").content,
        bc_URL: document.querySelector("meta[name=bc_websocket]").content,
      },
      interval: [
        {
          label: "Second",
          value: "second",
          icon: "fa fa-stopwatch-20",
        },
        {
          label: "Minute",
          value: "minute",
          icon: "fa fa-hourglass-start",
        },
        {
          label: "5 Minutes",
          value: "average",
          icon: "fa fa-hourglass-half",
        },
        {
          label: "Hourly",
          value: "hourly",
          icon: "fa fa-clock",
        },
        {
          label: "Daily",
          value: "daily",
          icon: "fa fa-calendar-day",
        },
        {
          label: "Monthly",
          value: "monthly",
          icon: "fa fa-calendar-alt",
        },
      ],
      type: [
        {
          label: "Terukur",
          value: "u",
        },
        {
          label: "Terkoreksi",
          value: "k",
        },
      ],
      view: [
        {
          icon: "fa fa-border-all",
          value: "table",
        },
        {
          icon: "fa fa-chart-line",
          value: "chart",
        },
        {
          icon: "fa fa-clone",
          value: "simple",
        },
      ],
      input: ["text", "number", "radio", "checkbox", "option", "textarea"],
      grid: [3, 4],
    },
    model: {
      cemsId: 1,
      parameter: [],
      timeFrom: moment().add("-60", "minutes").format("YYYY-MM-DD HH:mm"),
      timeTo: moment().add("-1", "minutes").format("YYYY-MM-DD HH:mm"),
      interval: "minute",
      flag: "u",
      view: localStorage.getItem("view") ?? "simple",
      grid: 3,
      times: {
        start: new Date(moment().add("-1", "hours")),
        end: new Date(),
      },
    },
    observer: null,
  }),
  computed: {
    dark() {
      return theme.value == "dark" ? true : false;
    },
    themes() {
      return theme.value;
    },
  },
  mounted() {
    window.addEventListener(
      "contextmenu",
      function (e) {
        e.preventDefault();
      },
      false
    );
    setTimeout(() => {
      let tooltip = document.querySelectorAll('[data-toggle="tooltip"]');
      tooltip.forEach((t) => {
        new coreui.Tooltip(t, {});
      });
    }, 1000);
  },
  methods: {
    connectWebSocket(url) {
      return new Promise((resolve, reject) => {
        if ("WebSocket" in window) {
          this.connection = new WebSocket(url);
          if (this.connection.readyState == 0) {
            swal.fire({
              icon: "info",
              title: "Trying to connect Server",
              toast: true,
              showConfirmButton: false,
              position: "top-end",
            });
          }
          if (this.connection.readyState == 1) {
            swal.fire({
              icon: "info",
              title: "Connection has been made",
              toast: true,
              showConfirmButton: false,
              position: "top-end",
            });
          }
          this.connection.onopen = () => {
            swal.fire({
              icon: "info",
              title: "Connected to Server",
              toast: true,
              showConfirmButton: false,
              position: "top-end",
              timer: 2000,
            });

            this.control.loading = false;
            resolve();
          };
          this.connection.onerror = (e) => reject(e);
          this.connection.onclose = (e) => reject(e);
        } else {
          reject(
            "Your browser doesn't support connect requirement. </br> Please update your browser"
          );
        }
      });
    },
    generateRandomColor() {
      let letters = "0123456789ABCDEF";
      let color = "#";
      for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
      }
      return color;
    },
    generateRandomNumber(min, max) {
      min = Math.ceil(min);
      max = Math.floor(max);
      return Math.floor(Math.random() * (max - min + 1)) + min;
    },
    waiting() {
      swal.fire({
        icon: "info",
        title: "Please wait",
        showConfirmButton: false,
        allowOutsideClick: false,
        toast: true,
        position: "top-right",
      });
    },
    notfound() {
      swal.fire({
        icon: "info",
        title: "Data not found or empty",
        text: "Try changing time ranges",
        showConfirmButton: false,
        allowOutsideClick: false,
        toast: true,
        position: "top-right",
        timer: 5000,
      });
    },
    error() {
      swal.fire({
        icon: "error",
        title: "Error getting data",
        text: "Server was busy or network timeout",
        showConfirmButton: false,
        allowOutsideClick: false,
        toast: true,
        position: "top-right",
        timer: 5000,
      });
    },
  },
});
$(function () {
  $(".timepicker").daterangepicker({
    timePicker: true,
    timePicker24Hour: true,
    startDate: moment().add("-60", "minutes"),
    endDate: moment(),
    maxDate: moment(),
    locale: {
      format: "YYYY-MM-DD HH:mm",
    },
  });
  $(".timepicker span").html(
    `${moment()
      .add("-60", "minutes")
      .format("YYYY-MM-DD HH:mm")} ~ ${moment().format("YYYY-MM-DD HH:mm")}`
  );

  $(document).on("click", ".btn", function (e) {
    $btn = $(this);
    var $offset = $(this).offset();
    $span = $("<span/>");
    var x = e.pageX - $offset.left;
    var y = e.pageY - $offset.top;
    $span.addClass("ripple-span");
    $span.css({
      top: y + "px",
      left: x + "px",
    });
    $btn.append($span);
    window.setTimeout(function () {
      $span.remove();
    }, 2200);
  });
});
document.onreadystatechange = () => {
  if (document.readyState == "complete") {
    document.body.dataset.theme =
      JSON.parse(localStorage.getItem("dark")) == true ? "dark" : "light";
    if (JSON.parse(localStorage.getItem("dark")) == true) {
      document.querySelector("#icon-theme")?.classList.remove("cil-moon");
      document.querySelector("#icon-theme")?.classList.add("cil-sun");
      theme.value = "dark";
    } else {
      document.querySelector("#icon-theme")?.classList.add("cil-moon");
      document.querySelector("#icon-theme")?.classList.remove("cil-sun");
      theme.value = "light";
    }
  }
};
function changeTheme() {
  let current = document.body.dataset.theme;
  if (current == "light") {
    document.querySelector("#icon-theme")?.classList.remove("cil-moon");
    document.querySelector("#icon-theme")?.classList.add("cil-sun");
    document.body.dataset.theme = "dark";
    // localStorage.setItem('dark', true)
    document.cookie = "dark=true";
    theme.value = "dark";
  } else {
    document.querySelector("#icon-theme")?.classList.add("cil-moon");
    document.querySelector("#icon-theme")?.classList.remove("cil-sun");
    document.body.dataset.theme = "light";
    // localStorage.setItem('dark', false)
    document.cookie = "dark=false";
    theme.value = "light";
  }
}

function ValidateEmail(mail) {
  if (
    /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(
      mail
    )
  ) {
    return true;
  }
  return false;
}
