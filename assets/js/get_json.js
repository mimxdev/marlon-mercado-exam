const { createApp } = Vue;
createApp({
  data() {
    return {
      videoData: [],
      channelData: [],
      pageSize: 20,
      currentPage: 1,
    };
  },
  created: function () {
    fetch('./youtube_channel_json.php')
      .then((r) => r.json())
      .then((videoData) => {
        this.videoData = videoData.videos;
      });
    fetch('./youtube_channel_json.php')
      .then((r) => r.json())
      .then((channelData) => {
        this.channelData = channelData.channels;
      });
  },
  methods: {
    nextPage: function () {
      if (this.currentPage * this.pageSize < this.videoData.length)
        this.currentPage++;
    },
    prevPage: function () {
      if (this.currentPage > 1) this.currentPage--;
    },
  },
  computed: {
    sortedVideos: function () {
      return this.videoData.filter((row, index) => {
        let start = (this.currentPage - 1) * this.pageSize;
        let end = this.currentPage * this.pageSize;
        if (index >= start && index < end) return true;
      });
    },
  },
}).mount('#app');
