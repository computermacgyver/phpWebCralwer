#Launch R in this directory then run this script, i.e.
# R --vanilla <size_strong_component.R> compontent_sizes_log.out.txt

library(igraph)

files<-list.files(pattern="\\.graphml$")
N<-length(files)
OUT <- data.frame(file=rep("", N), totalNodes=rep(NA, N),size=rep(NA, N),percent=rep(NA, N),stringsAsFactors=FALSE)
i<-1
for (file in files) {
	print(file)
	
	g<-read.graph(file,"graphml")
	summary(g)
	res<-clusters(g, mode="strong")
	print(max(res$csize))
	print(max(res$csize)/length(V(g)))
	#dist<-cluster.distribution(g, cumulative = FALSE)
	
	OUT[i, ] <- c(file, length(V(g)),max(res$csize),max(res$csize)/length(V(g)))
	i<-i+1
}

#Output
#filename,total_nodes,nodes_in_strong,percent
write.csv(OUT,"compontent_sizes.out.csv")
