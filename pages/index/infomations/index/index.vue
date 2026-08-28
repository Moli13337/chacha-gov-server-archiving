<template>
	<div class="complaint-container">
		<!-- 面包屑 -->
		<bread-crumb/>
		<div class="tip-box">
			<div class="tip">资讯</div>
		</div>
		<div class="content">
			<ul
				class="text-content"
				v-for="(item, index) in list"
				:key="index"
			>
				<li
					class="content-item"
					@click="handleToDetaile(item.id)"
				>
					<p><span class="icon-tip"></span>
						<span class="text">{{item.title}}</span>
					</p>
					<p>{{item.time}}</p>
				</li>
				<el-divider v-if="index != 0 && index % 5 ===0"></el-divider>
			</ul>
		</div>
		<div class="pagination-container">
			<el-pagination
				background
				layout="prev, pager, next"
				:total="1000"
				prev-text="上一页"
				next-text="下一页"
				class="pagination"
			>
			</el-pagination>
		</div>
		<side-tips/>
	</div>
</template>
<script>
import BreadCrumb from '~/components/breadcrumb';
import SideTips from '~/components/side-suggest';
export default {
	components: {
		BreadCrumb,
		SideTips
	},
	data() {
		return {
			ruleForm: {
				title: '',
				content: '',
				key: '',
				captcha: ''
			},
			list: [
				{
					id: 1,
					title: '成都市温江区中小企业发展专项资金项目申报',
					time: '2019-05-09'
				},
				{
					id: 2,
					title: '成都市温江区中小企业发展专项资金项目申报',
					time: '2019-05-09'
				},
				{
					id: 3,
					title: '成都市温江区中小企业发展专项资金项目申报',
					time: '2019-05-09'
				},
				{
					id: 4,
					title: '成都市温江区中小企业发展专项资金项目申报',
					time: '2019-05-09'
				},
				{
					id: 5,
					title: '成都市温江区中小企业发展专项资金项目申报',
					time: '2019-05-09'
				},
				{
					id: 6,
					title: '成都市温江区中小企业发展专项资金项目申报',
					time: '2019-05-09'
				},
				{
					id: 7,
					title: '成都市温江区中小企业发展专项资金项目申报',
					time: '2019-05-09'
				},
				{
					id: 8,
					title: '成都市温江区中小企业发展专项资金项目申报',
					time: '2019-05-09'
				},
				{
					id: 9,
					title: '成都市温江区中小企业发展专项资金项目申报',
					time: '2019-05-09'
				},
				{
					id: 10,
					title: '成都市温江区中小企业发展专项资金项目申报',
					time: '2019-05-09'
				},
				{
					id: 11,
					title: '成都市温江区中小企业发展专项资金项目申报',
					time: '2019-05-09'
				},
				{
					id: 12,
					title: '成都市温江区中小企业发展专项资金项目申报',
					time: '2019-05-09'
				},
				{
					id: 13,
					title: '成都市温江区中小企业发展专项资金项目申报',
					time: '2019-05-09'
				},
				{
					id: 14,
					title: '成都市温江区中小企业发展专项资金项目申报',
					time: '2019-05-09'
				},
				{
					id: 15,
					title: '成都市温江区中小企业发展专项资金项目申报',
					time: '2019-05-09'
				},

			]
		};
	},
	methods: {
		onSubmit() {
			console.log(this.ruleForm);
		},
		handleToDetaile(id) {
			this.$router.push({name: 'index-infomations-index-id', params: {id: id}});
		}
	},
	// 独立设置head信息
	head() {
		return {
			title: this.title,
			meta: [
				{hid: 'home', breadcrumbName: '我要投诉', path: '/'}
			]
		};
	},
	mounted() {
		console.log(this.$route.matched);
	},
	// 初始化图片验证码，返回data数据
	async asyncData({$axios}) {
		let {key, img} = await $axios.get(FETCH_CAPTCHACODE);

		return {
			// 图形验证码图片地址
			captcha: img,
			// 图形验证码key值
			captchaKey: key
		};
	},
};
</script>
<style lang="less" >
@import '~assets/css/common_avairail.less';
.complaint-container {
  width: 100%;
  background: @backGroundColor;
  box-shadow:0px 0px 5px rgba(0,0,0,0.05);
  padding: 15px;
  .bread-crumb {
    line-height: 18px;
    border-bottom: 1px solid @borderLine;
    padding-bottom: 15px;
    .el-divider {
      width:5px;
      height:18px;
      background-color: @primaryColor;
    }
    .bread-crumb-tip {
      display: inline-block;
      height: 18px;
      line-height: 18px;
    }
  }
    .tip-box {
    background:@menuHoverBgColor;
    padding: 5px 0;
    }
    .tip {
      margin: 10px 0;
      width: 114px;
      height: 31px;
      line-height: 0.5px;
      background-color: @primaryColor;
      color: @backGroundColor;
      line-height: 31px;
      text-align: center;
      font-size:16px;
      font-family:Microsoft YaHei;
      font-weight:400;
    }
  .content {
    width: 100%;
    // height: 500px;
    border: 1px solid @borderLine;
    padding:20px 39px;

    .text-content {
      // padding:0 39px;
      color: @textColor;
      .content-item {
        line-height: 35px;
        display: flex;
        justify-content: space-between;
        font-size:16px;
        font-family:Microsoft YaHei;
        font-weight:400;
      }
      .icon-tip {
        display: inline-block;
        width: 8px;
        height: 8px;
        border: 1px solid @primaryColor;
      }
    }
  }
      .pagination-container {
          width: 100%;
          margin: 40px 0;
          display: flex;
          justify-content: center;
          .number{
            width: 42px;
            height: 41px;
            line-height: 41px;
            background: @backGroundColor;
          }
          .el-icon-more {
             background: none;
              height: 41px;
            line-height: 41px;
          }
          .btn-prev, .btn-next {
            height: 41px;
            width: 92px;
            font-size: 20px;
            background: @backGroundColor;
          }
        }
}
</style>

