<template>
  <div id="app">
  <div class="container">
    <img src="catgate.logo.png" class="logo" />

     
<!-- <el-form :inline="true" :model="form" >
  
  <el-form-item label="活动区域">
    <el-input v-model="form.name"></el-input>
  </el-form-item>

  <el-form-item>
    <el-button type="primary" >查询</el-button>
  </el-form-item>
</el-form> -->


  <el-form  :label-position="labelPosition" label-width="80px">
  
  <el-form-item label="要抓取的URL列表地址">
    <el-input v-model="fetch_urls_url"></el-input>
  </el-form-item>

<el-form-item >
<el-button type="primary" @click="load_url()" >载入列表数据</el-button> <el-button type="default" @click="fetch_urls_show = !fetch_urls_show">查看列表数据&nbsp;[{{url_num}}]</el-button> <el-button type="default" @click="show_auto = !show_auto">自动多次载入列表</el-button>
</el-form-item>

<el-form-item v-if="fetch_urls_show">
<ul class="urls" v-if="fetch_urls.length>0">
  <li v-for="u in fetch_urls">{{u}}</li>
</ul>
</el-form-item >

<el-form-item label="每多少秒自动载入列表一次？" v-if="show_auto">
  
  <el-input-number v-model="fetch_seconds"></el-input-number>&nbsp;&nbsp;&nbsp;<el-button type="primary" @click="auto_load()" >{{auto_text}}</el-button>
   
  

</el-form-item>
  

<el-form-item label="POSTURL">
  <el-input v-model="posturl"></el-input>
</el-form-item>

  

  

  
  <el-form-item label="每多少秒抓取一次？">
    <el-input-number v-model="fetch_seconds_2"></el-input-number>&nbsp;&nbsp;&nbsp; <el-button type="primary" @click="continue_it()" v-if="url_num>0">{{continue_text}}</el-button>

    <el-button type="danger" @click="retry()" v-if="bad_urls.length>0">重新抓取{{bad_urls.length}}条失败的URL</el-button>


  </el-form-item>


  <template v-if="flog.length>0">
    <div class="flog">{{flog}}</div>
  </template>

  <template>
    <div class="copyright"><a href="http://ftqq.com" target="_blank">方糖气球</a>🎈 荣誉出品</div>
  </template>




</el-form>
  
  </div>

  </div>
</template>

<script>
import $ from 'jquery'

export default {
  name: 'app',
  data () {
    return {
      
      show_auto: false,
      fetch_auto: false,
      fetch_urls_url: 'http://localhost:8000/list.php',
      fetch_urls_show: false,
      fetch_urls:[],
      bad_urls:[],
      posturl: 'http://localhost:8000/save.php',
      fetch_seconds: 1,
      fetch_seconds_2: 1,
      labelPosition: 'top',
      timer: null,
      fetch_disabled:false,
      flog:'',
      continue:false
    }
  },
  computed:
  {
    url_num:function()
    {
      return this.fetch_urls.length;
    },
    auto_text:function()
    {
      if( this.fetch_seconds == 0 ) return '停止载入';
      else return '自动载入';  
    },
    continue_text:function()
    {
      if( this.continue ) return '抓取中...点我暂停';
      else return '点我抓取';
    },
    fetch_enable:function()
    {
      return this.fetch_urls.length > 0;
    }
  },
  methods:
  {
    load_url()
    {
      $.get( this.fetch_urls_url , ( data ) => 
      {
        this.fetch_urls = $.unique( this.fetch_urls.concat( data )) ;
      });
    },
    retry()
    {
      this.fetch_urls = this.bad_urls;
      this.bad_urls = [];
      this.go_fetch();
    },
    continue_it()
    {
      if( !this.continue )
      {
        this.go_fetch();
      }
      else
      {
         this.continue = false;
         this.flog = '暂停抓取';
      }
      
    },
    go_fetch()
    {
      this.continue = true;
      this.flog = '开始抓取';
      this.fetch_url();
    }
    ,
    fetch_url()
    {
      if( this.fetch_urls.length < 1 )
      {
        this.flog = '所有URL已经抓取完成 🤠';
        this.continue = false;
        return true;
      }

      let now_url = this.fetch_urls.pop();
       $.ajax({
            url: now_url,
            type: 'GET',
            success:( data ) =>
            { 
              if( typeof data === 'object' ) data = JSON.stringify( data );

              $.post( this.posturl , { content: data } , ( data2 ) => 
              {
                this.flog = data2 + ' 😶';
              } );
            }
            ,error: (data) => 
            {
              this.bad_urls.unshift( now_url ); 
              this.flog = '抓取URL'+now_url+'失败 😔';           
            }
        });

       if( this.continue && this.fetch_seconds_2 > 0 )
       {
          setTimeout( this.fetch_url , this.fetch_seconds_2 * 1000 );
       }
    }
    ,
    auto_load()
    {
      if( this.fetch_seconds == 0  )
      {
        if( this.timer ) clearInterval( this.timer );
      }
      else
      {
         this.timer = setInterval( this.load_url , this.fetch_seconds * 1000 )
      }
     
    }
  }
}
</script>

<style>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

ul.urls
{
  max-height: 200px;
  overflow-y: scroll;
  border: 1px solid #ccc;
  padding: 10px;
}

ul.urls li
{
  display: block;
  border-bottom: 1px solid #eee;
}

ul.urls li:last-child
{
  border-bottom: 0px;
}

.flog
{
  margin-top: 20px;
  background-color: #99A9BF;
  color: white;
  padding: 10px;
  border-radius: 5px;
}

.copyright
{
  margin-top: 50px;
  color:#aaa;
  font-size:12px;
}

.copyright a
{
  color:#aaa;
  text-decoration:none;
}

.copyright a:hover
{
  text-decoration:underline;  
}

.logo
{
  margin-bottom: 20px;
}

.container
{
  padding: 20px;
  max-width: 600px ;
  margin-right: auto;
  margin-left: auto;
}

h1, h2 {
  font-weight: normal;
}


</style>
