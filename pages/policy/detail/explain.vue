<template>
	<div class="expalin-container">
		<!-- 面包屑 -->
		<div class="breadcrumb-row">
			<el-divider direction="vertical"></el-divider>
			当前位置：
			<el-breadcrumb separator-class="el-icon-arrow-right">
				<el-breadcrumb-item>
					<nuxt-link to="/">首页</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<nuxt-link to="/policy">政策详情</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<div>政策解读</div>
				</el-breadcrumb-item>
			</el-breadcrumb>
		</div>
		<div class="content">
			<div class="text-content">
				<div class="title-container">
					<p class="title">{{data.name}}</p>
					<div class="tips"><p><span>解读来源：</span>产服通智慧企业服务平台</p></div>
					<el-divider></el-divider>
				</div>
				<div class="text-container">
					<section>
						<p class="text type"> <el-divider direction="vertical"></el-divider>解读正文</p>
					</section>
					<pdf
						v-for="i in numPages"
						:key="i"
						:src="src"
						:page="i"
						style="display: inline-block; width: 100%"
					/>
					<section v-if="data.policy && data.policy.length > 0">
						<p class="type"> <el-divider direction="vertical"></el-divider>相关政策</p>
						<ul class="policy">
							<li
								v-for="(item, index) in data.policy"
								:key="index"
								@click="handleRelativeClick(item.id)"
							>{{item.name}}
							</li>
						</ul>
					</section>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
import Pdf from 'vue-pdf';
import CMapReaderFactory from 'vue-pdf/src/CMapReaderFactory.js';
import {
	QUERY_POLICY_EXPLAIN
} from '@/utils/urls.js';
export default {
	components: {
		Pdf,
	},
	data() {
		return {
			src: '',
			numPages: 0
		};
	},
	// 验证ID是否存在
	validate({query}) {
		return query.id !== undefined;
	},
	// 加载完解读数据后才显示页面
	async asyncData({query, $axios}) {
		const result = await $axios.get(QUERY_POLICY_EXPLAIN, {params: {id: query.id}});

		return {
			data: result || {}
		};
	},
	mounted() {
		console.log('content_url', this.data.content_url);
		if (this.data.content_url) {
			this.$nextTick(() => {
				this.src = Pdf.createLoadingTask({
					url: this.data.content_url,
					CMapReaderFactory
				});
				this.src.then(pdf => {
					this.numPages = pdf.numPages;
				});
			});
		}
	},
	methods: {
		// 处理相关政策详情跳转
		handleRelativeClick(id) {
			this.$router.push({
				name: 'policy-detail',
				query: {
					id
				}
			});
		}
	}
};
</script>
<style lang="less" >
@import '~assets/css/common_avairail.less';
@import '~assets/css/common.less';
.expalin-container {
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
  .content {
    width: 100%;
    border: 1px solid @borderLine;
    margin-top: 20px;
    .title-container {
      .title {
        font-size:31px;
        font-family:Microsoft YaHei;
        font-weight:400;
        margin: auto;
        width: 656px;
        color: #3B3B3B;
        padding: 20px 0;
        text-align: center;
      }
      .tips {
        font-size:14px;
        font-family:Microsoft YaHei;
        font-weight:400;
        width: 656px;
        margin: auto;
        text-align: center;
      }
    }
    .type {
      font-size:19px;
      font-family:Microsoft YaHei;
      font-weight:400;
      padding: 10px 0;
        .el-divider--vertical {
          width: 6px;
          background: @primaryColor;
          height: 27px;
          margin-left: 0;
        }
      }
      .policy {
        color: @poliyItemColor;
        line-height:25px;
      }
    .text-content {
      padding: 0 60px;
      color: @textColor;
      section {
        margin-bottom: 40px;
      }
    }

  }
}
</style>

